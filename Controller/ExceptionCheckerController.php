<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use c975L\ExceptionCheckerBundle\Entity\ExceptionChecker;
use c975L\ExceptionCheckerBundle\Form\ExceptionCheckerType;

class ExceptionCheckerController extends Controller
{
//DASHBOARD
    /**
     * @Route("/exception-checker/dashboard",
     *      name="exceptionchecker_dashboard")
     * @Method({"GET", "HEAD"})
     */
    public function dashboardAction(Request $request)
    {
        //Gets the user
        $user = $this->getUser();

        //Returns the dashboard content
        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_exception_checker.roleNeeded'))) {
            //Gets the manager
            $em = $this->getDoctrine()->getManager();

            //Gets repository
            $repository = $em->getRepository('c975LExceptionCheckerBundle:ExceptionChecker');

            //Gets the exceptionchecker
            $exceptionCheckers = $repository->findAll();

            //Pagination
            $paginator  = $this->get('knp_paginator');
            $pagination = $paginator->paginate(
                $exceptionCheckers,
                $request->query->getInt('p', 1),
                25
            );

            //Defines toolbar
            $tools  = $this->renderView('@c975LExceptionChecker/tools.html.twig', array(
                'type' => 'dashboard',
            ));
            $toolbar = $this->forward('c975L\ToolbarBundle\Controller\ToolbarController::displayAction', array(
                'tools'  => $tools,
                'dashboard'  => 'exceptionchecker',
            ))->getContent();

            //Returns the dashboard
            return $this->render('@c975LExceptionChecker/pages/dashboard.html.twig', array(
                'exceptionCheckers' => $pagination,
                'toolbar' => $toolbar,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//DISPLAY
    /**
     * @Route("/exception-checker/{id}",
     *      name="exceptionchecker_display",
     *      requirements={
     *          "id": "^([0-9]+)"
     *      })
     * @Method({"GET", "HEAD"})
     */
    public function displayAction($id)
    {
        //Gets the user
        $user = $this->getUser();

        //Defines the form
        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_exception_checker.roleNeeded'))) {
            //Gets the manager
            $em = $this->getDoctrine()->getManager();

            //Gets repository
            $repository = $em->getRepository('c975LExceptionCheckerBundle:ExceptionChecker');

            //Gets the exceptionChecker
            $exceptionChecker = $repository->findOneById($id);

            //Defines toolbar
            $tools  = $this->renderView('@c975LExceptionChecker/tools.html.twig', array(
                'type' => 'display',
                'exceptionChecker' => $exceptionChecker,
            ));
            $toolbar = $this->forward('c975L\ToolbarBundle\Controller\ToolbarController::displayAction', array(
                'tools'  => $tools,
                'dashboard'  => 'exceptionchecker',
            ))->getContent();

            return $this->render('@c975LExceptionChecker/pages/display.html.twig', array(
                'toolbar' => $toolbar,
                'exceptionChecker' => $exceptionChecker,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//NEW
    /**
     * @Route("/exception-checker/new",
     *      name="exceptionchecker_new")
     */
    public function newAction(Request $request)
    {
        //Gets the user
        $user = $this->getUser();

        //Defines the form
        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_exception_checker.roleNeeded'))) {
            //Defines form
            $exceptionChecker = new ExceptionChecker();
            $exceptionCheckerConfig = array(
                'action' => 'new',
            );
            $form = $this->createForm(ExceptionCheckerType::class, $exceptionChecker, array('exceptionCheckerConfig' => $exceptionCheckerConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Adds data
                $exceptionChecker->setCreation(new \DateTime());
                if ($exceptionChecker->getRedirectKind() == '') {
                    $exceptionChecker->setRedirectKind(null);
                }

                //Gets the manager
                $em = $this->getDoctrine()->getManager();

                //Persists data in DB
                $em->persist($exceptionChecker);
                $em->flush();

                //Redirects to the exceptionChecker
                return $this->redirectToRoute('exceptionchecker_display', array(
                    'id' => $exceptionChecker->getId(),
                ));
            }

            //Defines toolbar
            $tools  = $this->renderView('@c975LExceptionChecker/tools.html.twig', array(
                'type' => 'new',
            ));
            $toolbar = $this->forward('c975L\ToolbarBundle\Controller\ToolbarController::displayAction', array(
                'tools'  => $tools,
                'dashboard'  => 'exceptionchecker',
            ))->getContent();

            //Returns the form to edit content
            return $this->render('@c975LExceptionChecker/forms/new.html.twig', array(
                'form' => $form->createView(),
                'toolbar' => $toolbar,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//MODIFY
    /**
     * @Route("/exception-checker/modify/{id}",
     *      name="exceptionchecker_modify",
     *      requirements={
     *          "id": "^([0-9]+)"
     *      })
     */
    public function modifyAction(Request $request, $id)
    {
        //Gets the user
        $user = $this->getUser();

        //Defines the form
        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_exception_checker.roleNeeded'))) {
            //Gets the manager
            $em = $this->getDoctrine()->getManager();

            //Gets repository
            $repository = $em->getRepository('c975LExceptionCheckerBundle:ExceptionChecker');

            //Gets the exceptionChecker
            $exceptionChecker = $repository->findOneById($id);

            //Defines form
            $exceptionCheckerConfig = array(
                'action' => 'modify',
            );
            $form = $this->createForm(ExceptionCheckerType::class, $exceptionChecker, array('exceptionCheckerConfig' => $exceptionCheckerConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Adds data
                $exceptionChecker->setCreation(new \DateTime());
                if ($exceptionChecker->getRedirectKind() == '') {
                    $exceptionChecker->setRedirectKind(null);
                    $exceptionChecker->setRedirectData(null);
                }

                //Persists data in DB
                $em->persist($exceptionChecker);
                $em->flush();

                //Redirects to the exceptionChecker
                return $this->redirectToRoute('exceptionchecker_display', array(
                    'id' => $exceptionChecker->getId(),
                ));
            }

            //Defines toolbar
            $tools  = $this->renderView('@c975LExceptionChecker/tools.html.twig', array(
                'type' => 'modify',
                'exceptionChecker' => $exceptionChecker,
            ));
            $toolbar = $this->forward('c975L\ToolbarBundle\Controller\ToolbarController::displayAction', array(
                'tools'  => $tools,
                'dashboard'  => 'exceptionchecker',
            ))->getContent();

            //Returns the form to modify content
            return $this->render('@c975LExceptionChecker/forms/modify.html.twig', array(
                'form' => $form->createView(),
                'exceptionChecker' => $exceptionChecker,
                'toolbar' => $toolbar,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//DUPLICATE
    /**
     * @Route("/exception-checker/duplicate/{id}",
     *      name="exceptionchecker_duplicate",
     *      requirements={
     *          "id": "^[0-9]+$"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function duplicateAction(Request $request, $id)
    {
        //Gets the user
        $user = $this->getUser();

        //Defines the form
        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_exception_checker.roleNeeded'))) {
            //Gets the manager
            $em = $this->getDoctrine()->getManager();

            //Gets repository
            $repository = $em->getRepository('c975LExceptionCheckerBundle:ExceptionChecker');

            //Gets the exceptionChecker
            $exceptionChecker = $repository->findOneById($id);

            //Defines form
            $exceptionCheckerClone = clone $exceptionChecker;
            $exceptionCheckerConfig = array(
                'action' => 'duplicate',
            );
            $form = $this->createForm(ExceptionCheckerType::class, $exceptionCheckerClone, array('exceptionCheckerConfig' => $exceptionCheckerConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Adds data
                $exceptionChecker->setCreation(new \DateTime());
                if ($exceptionChecker->getRedirectKind() == '') {
                    $exceptionChecker->setRedirectKind(null);
                    $exceptionChecker->setRedirectData(null);
                }

                //Persists data in DB
                $em->persist($exceptionCheckerClone);
                $em->flush();

                //Redirects to the exceptionChecker
                return $this->redirectToRoute('exceptionchecker_display', array(
                    'id' => $exceptionChecker->getId(),
                ));
            }

            //Defines toolbar
            $tools  = $this->renderView('@c975LExceptionChecker/tools.html.twig', array(
                'type' => 'duplicate',
                'exceptionChecker' => $exceptionChecker,
            ));
            $toolbar = $this->forward('c975L\ToolbarBundle\Controller\ToolbarController::displayAction', array(
                'tools'  => $tools,
                'dashboard'  => 'exceptionchecker',
            ))->getContent();

            //Returns the form to duplicate content
            return $this->render('@c975LExceptionChecker/forms/duplicate.html.twig', array(
                'form' => $form->createView(),
                'exceptionChecker' => $exceptionCheckerClone,
                'toolbar' => $toolbar,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//DELETE
    /**
     * @Route("/exception-checker/delete/{id}",
     *      name="exceptionchecker_delete",
     *      requirements={
     *          "id": "^([0-9]+)"
     *      })
     */
    public function deleteAction(Request $request, $id)
    {
        //Gets the user
        $user = $this->getUser();

        //Defines the form
        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_exception_checker.roleNeeded'))) {
            //Gets the manager
            $em = $this->getDoctrine()->getManager();

            //Gets repository
            $repository = $em->getRepository('c975LExceptionCheckerBundle:ExceptionChecker');

            //Gets the exceptionChecker
            $exceptionChecker = $repository->findOneById($id);

            //Defines form
            $exceptionCheckerConfig = array(
                'action' => 'delete',
            );
            $form = $this->createForm(ExceptionCheckerType::class, $exceptionChecker, array('exceptionCheckerConfig' => $exceptionCheckerConfig));
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                //Persists data in DB
                $em->remove($exceptionChecker);
                $em->flush();

                //Redirects to the dashboard
                return $this->redirectToRoute('exceptionchecker_dashboard');
            }

            //Defines toolbar
            $tools  = $this->renderView('@c975LExceptionChecker/tools.html.twig', array(
                'type' => 'delete',
                'exceptionChecker' => $exceptionChecker,
            ));
            $toolbar = $this->forward('c975L\ToolbarBundle\Controller\ToolbarController::displayAction', array(
                'tools'  => $tools,
                'dashboard'  => 'exceptionchecker',
            ))->getContent();

            //Returns the form to delete content
            return $this->render('@c975LExceptionChecker/forms/delete.html.twig', array(
                'form' => $form->createView(),
                'exceptionChecker' => $exceptionChecker,
                'toolbar' => $toolbar,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//HELP
    /**
     * @Route("/exception-checker/help",
     *      name="exceptionchecker_help")
     * @Method({"GET", "HEAD"})
     */
    public function helpAction()
    {
        //Gets the user
        $user = $this->getUser();

        //Returns the dashboard content
        if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_exception_checker.roleNeeded'))) {
            //Defines toolbar
            $tools  = $this->renderView('@c975LExceptionChecker/tools.html.twig', array(
                'type' => 'help',
            ));
            $toolbar = $this->forward('c975L\ToolbarBundle\Controller\ToolbarController::displayAction', array(
                'tools'  => $tools,
                'dashboard'  => 'exceptionchecker',
            ))->getContent();

            //Returns the help
            return $this->render('@c975LExceptionChecker/pages/help.html.twig', array(
                'toolbar' => $toolbar,
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }
}