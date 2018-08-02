<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Listener;

use Symfony\Component\HttpKernel\Event\GetResponseForExceptionEvent;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class ExceptionListener
{
    private $container;
    private $em;
    private $logger;
    private $router;

    public function __construct(
        \Symfony\Component\DependencyInjection\ContainerInterface $container,
        \Doctrine\ORM\EntityManagerInterface $em,
        \Psr\Log\LoggerInterface $logger,
        \Symfony\Component\Routing\RouterInterface $router
        ) {
        $this->container = $container;
        $this->em = $em;
        $this->logger = $logger;
        $this->router = $router;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        //Gets exception
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
        if ($exceptionContinue === true) {
            //Gets url requested
            $url = $event->getRequest()->getPathInfo();

            if ($url !== null) {
                //Gets repository
                $repository = $this->em->getRepository('c975LExceptionCheckerBundle:ExceptionChecker');

                //Gets the exceptionChecker
                $exceptionChecker = $repository->findByUrl($url);

                //Checks with wildcards if not found
                if ($exceptionChecker === null) {
                    $exceptionCheckersWildcard = $repository->findWildcard();

                    if ($exceptionCheckersWildcard !== null) {
                        foreach ($exceptionCheckersWildcard as $exceptionCheckerWildcard) {
                            if (stripos($url, str_replace('*', '', $exceptionCheckerWildcard->getUrl())) !== false) {
                                $exceptionChecker = $exceptionCheckerWildcard;
                                break;
                            }
                        }
                    }
                }

                //ExceptionChecker has been found
                if ($exceptionChecker !== null) {
                    //Deleted
                    if ($exceptionChecker->getKind() == 'deleted') {
                        $event->setException(new GoneHttpException($url));
                    //Excluded
                    } elseif ($exceptionChecker->getKind() == 'excluded') {
                        $redirectUrl = $this->router->generate($this->container->getParameter('c975_l_exception_checker.redirectExcluded'));
                    //Redirected
                    } elseif ($exceptionChecker->getKind() == 'redirected') {
                        //Asset
                        if ($exceptionChecker->getRedirectKind() == 'Asset') {
                            $redirectUrl = str_replace('/app_dev.php', '', $this->router->getContext()->getBaseUrl()) . $exceptionChecker->getRedirectData();
                        //Url
                        } elseif ($exceptionChecker->getRedirectKind() == 'Url') {
                            $redirectUrl = $exceptionChecker->getRedirectData();
                        //Route
                        } elseif ($exceptionChecker->getRedirectKind() == 'Route') {
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
