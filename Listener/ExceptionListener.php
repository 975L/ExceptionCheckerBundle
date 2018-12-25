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
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
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
    /**
     * Stores ConfigServiceInterface
     * @var ConfigServiceInterface
     */
    private $configService;

    /**
     * Stores EntityManagerInterface
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Stores LoggerInterface
     * @var LoggerInterface
     */
    private $logger;

    /**
     * Stores RouterInterface
     * @var RouterInterface
     */
    private $router;

    public function __construct(
        ConfigServiceInterface $configService,
        EntityManagerInterface $em,
        LoggerInterface $logger,
        RouterInterface $router
    )
    {
        $this->configService = $configService;
        $this->em = $em;
        $this->logger = $logger;
        $this->router = $router;
    }

    /**
     * Catches the Exception
     */
    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        //Gets Exception
        $exception = $event->getException();

        //Checks if Exception is supported
        $exceptionContinue = false;
        $supportedExceptions = array(
            'Symfony\Component\HttpKernel\Exception\HttpException',
            'Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException',
            'Symfony\Component\HttpKernel\Exception\NotAcceptableHttpException',
            'Symfony\Component\HttpKernel\Exception\NotFoundHttpException',
        );
        foreach ($supportedExceptions as $supportedException) {
            if ($exception instanceof $supportedException) {
                $exceptionContinue = true;
                break;
            }
        }

        //Exception is supported
        if ($exceptionContinue) {
            //Gets url requested
            $url = $event->getRequest()->getPathInfo();

            if (null !== $url) {
                //Gets the ExceptionChecker
                $repository = $this->em->getRepository('c975LExceptionCheckerBundle:ExceptionChecker');
                $exceptionChecker = $repository->findByUrl($url);

                //Checks with wildcards if not found
                if (null === $exceptionChecker) {
                    $exceptionCheckersWildcard = $repository->findWildcard();

                    if (null !== $exceptionCheckersWildcard) {
                        foreach ($exceptionCheckersWildcard as $exceptionCheckerWildcard) {
                            if (false !== stripos($url, str_replace('*', '', $exceptionCheckerWildcard->getUrl()))) {
                                $exceptionChecker = $exceptionCheckerWildcard;
                                break;
                            }
                        }
                    }
                }

                //ExceptionChecker has been found
                if (null !== $exceptionChecker) {
                    //Deleted - Throws GoneHttpException
                    if ('deleted' == $exceptionChecker->getKind()) {
                        $event->setException(new GoneHttpException($url));
                    //Excluded - Redirects to defined Route
                    } elseif ('excluded' == $exceptionChecker->getKind()) {
                        $redirectUrl = $this->router->generate($this->configService->getParameter('c975LExceptionChecker.redirectExcluded'));
                    //Redirected - Redirects to defined redirection
                    } elseif ('redirected' == $exceptionChecker->getKind()) {
                        //Asset
                        if ('Asset' == $exceptionChecker->getRedirectKind()) {
                            $redirectUrl = str_replace('/app_dev.php', '', $this->router->getContext()->getBaseUrl()) . $exceptionChecker->getRedirectData();
                        //Url
                        } elseif ('Url' == $exceptionChecker->getRedirectKind()) {
                            $redirectUrl = $exceptionChecker->getRedirectData();
                        //Route
                        } elseif ('Route' == $exceptionChecker->getRedirectKind()) {
                            //Gets Route parameters
                            $parameters = array();
                            $parametersFinal = array();
                            if (strpos($exceptionChecker->getRedirectData(), '[') !== false) {
                                preg_match('/\[.*\]/s', $exceptionChecker->getRedirectData(), $parameters);
                                $parametersData = str_replace(array('[', ']'), '', $parameters[0]);
                                $parametersAll = explode(',', $parametersData);
                                foreach ($parametersAll as $parameter) {
                                    $paramData = explode('=>', $parameter);
                                    $key = trim(str_replace(array('"', "'"), '', $paramData[0]));
                                    $value = trim(str_replace(array('"', "'"), '', $paramData[1]));
                                    $parametersFinal[$key] = $value;
                                }
                            }

                            $redirectUrl = $this->router->generate(str_replace($parameters, '', $exceptionChecker->getRedirectData()), $parametersFinal);
                        }
                    }

                    //Updates Response
                    if (isset($redirectUrl)) {
                        $response = new RedirectResponse($redirectUrl);
                        $event->setResponse($response);
                    }
                //Adds link to exclude to log (useful if log is sent by email)
                } else {
                    $exceptionAddUrl = $this->router->generate('exceptionchecker_create_from_url', array('kind' => 'excluded'), UrlGeneratorInterface::ABSOLUTE_URL) . '?u=' . $url;
                    $this->logger->info('-----> Add to ExceptionChecker? Use ' . $exceptionAddUrl . ' with your secret code!');
                }
            }
        }
    }
}
