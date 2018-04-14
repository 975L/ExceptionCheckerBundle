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
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpFoundation\RedirectResponse;

class ExceptionListener
{
    private $container;
    private $router;

    public function __construct(
        \Symfony\Component\DependencyInjection\ContainerInterface $container,
        \Symfony\Bundle\FrameworkBundle\Routing\Router $router
        ) {
        $this->container = $container;
        $this->router = $router;
    }

    public function onKernelException(GetResponseForExceptionEvent $event)
    {
        //Gets exception
        $exception = $event->getException();

        if ($exception instanceof NotFoundHttpException) {
            //Gets url requested
            $url = $event->getRequest()->getPathInfo();

            if ($url !== null) {
                $folderPath = $this->container->getParameter('kernel.root_dir') . '/ExceptionChecker/';
                //Checks if url has been deleted
                $deletedUrlsFile = $folderPath . 'deletedUrls.txt';
                if (is_file($deletedUrlsFile)) {
                    $deletedUrls = file_get_contents($deletedUrlsFile);
                    $pattern = '/^' . preg_quote($url, '/') . '$/m';
                    //Sends a Gone Exception
                    if(preg_match_all($pattern, $deletedUrls, $matches)){
                        throw new GoneHttpException();
                    }
                }

                //Checks if url has been redirected
                $redirectedUrlsFile = $folderPath . 'redirectedUrls.txt';
                if (is_file($redirectedUrlsFile)) {
                    $redirectedUrls = str_replace(array(' #', ' #', '# ', '# '), '#', file_get_contents($redirectedUrlsFile));
                    $pattern = '/^' . preg_quote($url, '/') . '#.*/m';

                    //Redirects to new url
                    if(preg_match_all($pattern, $redirectedUrls, $matches)){
                        $redirect = explode('#', $matches[0][0]);
                        $redirectData = explode(':', $redirect[1]);

                        //Gets url provided
                        if ($redirectData[0] == 'Url') {
                            $redirectUrl = $redirectData[1];
                            //Url is absolute and has been split by explode
                            if (isset($redirectData[2])) {
                                $redirectUrl .= ':' . $redirectData[2];
                            }
                        //Builds url from Asset provided
                        } elseif ($redirectData[0] == 'Asset') {
                            $redirectUrl = str_replace('/app_dev.php', '', $this->router->getContext()->getBaseUrl()) . $redirectData[1];
                        //Builds url from Route provided
                        } elseif ($redirectData[0] == 'Route') {
                            //Gets Route parameters
                            $parameters = array();
                            $parametersFinal = array();
                            if (strpos($redirectData[1], '[') !== false) {
                                preg_match('/\[.*\]/s', $redirectData[1], $parameters);
                                $parametersData = str_replace(array('[', ']'), '', $parameters[0]);
                                $parametersAll = explode(',', $parametersData);
                                foreach ($parametersAll as $parameter) {
                                    $paramData = explode('=>', $parameter);
                                    $key = trim(str_replace(array('"', "'"), '', $paramData[0]));
                                    $value = trim(str_replace(array('"', "'"), '', $paramData[1]));
                                    $parametersFinal[$key] = $value;
                                }
                            }
                            $redirectUrl = $this->router->generate(str_replace($parameters, '', $redirectData[1]), $parametersFinal);
                        }

                        //Updates Response
                        if (isset($redirectUrl)) {
                            $response = new RedirectResponse($redirectUrl);
                            $event->setResponse($response);
                        }
                    }
                }
            }
        }
    }
}
