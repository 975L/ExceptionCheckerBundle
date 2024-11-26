<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Listener;

use c975L\ConfigBundle\Service\ConfigServiceInterface;
use c975L\ExceptionCheckerBundle\Entity\ExceptionChecker;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RouterInterface;

/**
 * ExceptionListener to catch Exception launch
 *
 * - Catches the Exception,
 * - Checks if it's a supported one,
 * - Searches in DB if registered ExceptionChecker
 * - Updates Response for "redirected" (Data defined in ExceptionChecker) and "excluded" (Route defined in config)
 * - Throws a "GoneHttpException" for "deleted"
 * - Throws a "BadRequestHttpException" for "ignored"
 * - Otherwise adds a message + link to the logger to easily add the ExceptionChecker to the DB
 *
 * Supported Exceptions
 *
 * - HttpException
 * - MethodNotAllowedHttpException
 * - NotAcceptableHttpException
 * - NotFoundHttpException
 *
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2017 975L <contact@975l.com>
 */
class ExceptionListener
{
    public function __construct(
        /**
         * Stores ConfigServiceInterface
         */
        private readonly ConfigServiceInterface $configService,
        /**
         * Stores EntityManagerInterface
         */
        private readonly EntityManagerInterface $em,
        /**
         * Stores LoggerInterface
         */
        private readonly LoggerInterface $logger,
        /**
         * Stores RouterInterface
         */
        private readonly RouterInterface $router
    )
    {
    }

    /**
     * Catches the Exception
     */
    public function onKernelException(ExceptionEvent $event)
    {
        // Gets Exception
        $exception = $event->getThrowable();

        // Checks if Exception is supported
        $exceptionContinue = false;
        $supportedExceptions = [
            \Symfony\Component\HttpKernel\Exception\HttpException::class,
            \Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException::class,
            \Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException::class,
            \Symfony\Component\HttpKernel\Exception\NotFoundHttpException::class
        ];
        foreach ($supportedExceptions as $supportedException) {
            if ($exception instanceof $supportedException) {
                $exceptionContinue = true;
                break;
            }
        }

        // Exception is supported
        if ($exceptionContinue) {
            // Gets url requested
            $url = $event->getRequest()->getPathInfo();

            if (null !== $url) {
                // Gets the ExceptionChecker
                $repository = $this->em->getRepository(ExceptionChecker::class);
                $exceptionChecker = $repository->findByUrl($url);

                // Checks with wildcards if not found
                if (null === $exceptionChecker) {
                    $exceptionCheckersWildcard = $repository->findWildcard();

                    if (null !== $exceptionCheckersWildcard) {
                        foreach ($exceptionCheckersWildcard as $exceptionCheckerWildcard) {
                            if (false !== stripos($url, (string) str_replace('*', '', (string) $exceptionCheckerWildcard->getUrl()))) {
                                $exceptionChecker = $exceptionCheckerWildcard;
                                break;
                            }
                        }
                    }
                }

                // Checks if url exist with other character case
                if (null === $exceptionChecker) {
                    $rootPath = $event->getRequest()->server->get('DOCUMENT_ROOT');
                    $basePath = $event->getRequest()->getBasePath();
                    $folder = $rootPath . $basePath;

                    if ('' !== $folder) {
                        $finder = new Finder();
                        $finder
                            ->files()
                            ->in($folder)
                            ->sortByName()
                        ;

                        foreach ($finder as $file) {
                            $fileRealPath = $file->getRealPath();
                            $filePath = substr($fileRealPath, strrpos($fileRealPath, $basePath) + strlen($basePath));
                            if ($rootPath . strtolower($url) === strtolower($filePath)) {
                                $exceptionChecker = new ExceptionChecker();
                                $exceptionChecker
                                    ->setKind('redirected')
                                    ->setRedirectKind('Asset')
                                    ->setRedirectData(str_replace($rootPath, '', $filePath))
                                ;
                                break;
                            }
                        }
                    }
                }

                // Checks if url has a trailing slash
                if (null === $exceptionChecker && '/' === substr($url, -1)) {
                    $exceptionChecker = new ExceptionChecker();
                    $exceptionChecker
                        ->setKind('redirected')
                        ->setRedirectKind('Url')
                        ->setRedirectData(substr($url, 0, strlen($url) - 1));
                    ;
                }

                // ExceptionChecker has been found
                if (null !== $exceptionChecker) {
                    // Deleted - Throws GoneHttpException
                    if ('deleted' == $exceptionChecker->getKind()) {
                        $event->setThrowable(new GoneHttpException($url));
                    // Excluded - Redirects to defined Route
                    } elseif ('excluded' === $exceptionChecker->getKind()) {
                        $redirectUrl = $this->router->generate($this->configService->getParameter('c975LExceptionChecker.redirectExcluded'));
                    // Ignored - Throws BadRequestHttpException
                    } elseif ('ignored' == $exceptionChecker->getKind()) {
                        $event->setThrowable(new BadRequestHttpException($url));
                    // Redirected - Redirects to defined redirection
                    } elseif ('redirected' === $exceptionChecker->getKind()) {
                        // Asset
                        if ('Asset' === $exceptionChecker->getRedirectKind()) {
                            $redirectUrl = str_replace('/app_dev.php', '', $this->router->getContext()->getBaseUrl()) . $exceptionChecker->getRedirectData();
                        // Url
                        } elseif ('Url' === $exceptionChecker->getRedirectKind()) {
                            $redirectUrl = $exceptionChecker->getRedirectData();
                        // Route
                        } elseif ('Route' === $exceptionChecker->getRedirectKind()) {
                            // Gets Route parameters
                            $parameters = [];
                            $parametersFinal = [];
                            if (str_contains((string) $exceptionChecker->getRedirectData(), '[')) {
                                preg_match('/\[.*\]/s', (string) $exceptionChecker->getRedirectData(), $parameters);
                                $parametersData = str_replace(['[', ']'], '', (string) $parameters[0]);
                                $parametersAll = explode(',', $parametersData);
                                foreach ($parametersAll as $parameter) {
                                    $paramData = explode('=>', $parameter);
                                    $key = trim(str_replace(['"', "'"], '', $paramData[0]));
                                    $value = trim(str_replace(['"', "'"], '', $paramData[1]));
                                    $parametersFinal[$key] = $value;
                                }
                            }

                            $redirectUrl = $this->router->generate(str_replace($parameters, '', (string) $exceptionChecker->getRedirectData()), $parametersFinal);
                        }
                    }

                    // Updates Response if $redirectUrl not empty
                    if (isset($redirectUrl) && null !== $redirectUrl && '' !== $redirectUrl && !empty($redirectUrl)) {
                        $response = new RedirectResponse($redirectUrl);
                        $event->setResponse($response);
                    }
                // Adds link to exclude to log (useful if log is sent by email)
                } else {
                    $exceptionAddUrl = $this->router->generate('exceptionchecker_create_from_url', ['kind' => 'excluded'], UrlGeneratorInterface::ABSOLUTE_URL) . '?u=' . $url;
                    $this->logger->info('-----> Add to ExceptionChecker? Use ' . $exceptionAddUrl . ' with your secret code!');
                }
            }
        }
    }
}
