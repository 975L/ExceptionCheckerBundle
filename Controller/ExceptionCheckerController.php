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

            //Returns the dashboard
            return $this->render('@c975LExceptionChecker/pages/dashboard.html.twig', array(
                'exceptionCheckers' => $pagination,
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

            return $this->render('@c975LExceptionChecker/pages/display.html.twig', array(
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
     * @Method({"GET", "HEAD", "POST"})
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

            //Returns the form to edit content
            return $this->render('@c975LExceptionChecker/forms/new.html.twig', array(
                'form' => $form->createView(),
            ));
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }

//ADD
    /**
     * @Route("/ec-add/{kind}",
     *      name="exceptionchecker_add",
     *      requirements={
     *          "kind": "deleted|excluded"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function addAction(Request $request, $kind)
    {
        //Gets the user
        $user = $this->getUser();

        //Defines form
        $exceptionChecker = new ExceptionChecker();
        $exceptionChecker
            ->setKind($kind)
            ->setUrl($request->get('u'))
        ;
        $exceptionCheckerConfig = array(
            'action' => 'add',
            'user' => $user,
        );
        $form = $this->createForm(ExceptionCheckerType::class, $exceptionChecker, array('exceptionCheckerConfig' => $exceptionCheckerConfig));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Gets the translator
            $translator = $this->get('translator');

            //Adds url if user has rights
            if ($user !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_exception_checker.roleNeeded')) ||
                $form->get('secret')->getData() == $this->getParameter('exceptionCheckerSecret')) {
                //Gets the manager
                $em = $this->getDoctrine()->getManager();

                //Gets repository
                $repository = $em->getRepository('c975LExceptionCheckerBundle:ExceptionChecker');

                //Checks if exceptionChecker already exists
                $existing = $repository->findOneByUrl($request->get('u'));

                //ExceptionChecker url doesn't exist
                if ($existing === null) {
                    //Adds data
                    $exceptionChecker->setCreation(new \DateTime());

                    //Persists data in DB
                    $em->persist($exceptionChecker);
                    $em->flush();
                }

                //Creates flash
                $flash = $translator->trans('text.exception_checker_added', array('%url%' => $exceptionChecker->getUrl()), 'exceptionChecker');
                $request->getSession()
                    ->getFlashBag()
                    ->add('success', $flash);

                //Redirects to the exceptionChecker
                return $this->redirectToRoute($this->getParameter('c975_l_exception_checker.redirectExcluded'));
            //Wrong secret code
            } elseif ($form->get('secret')->getData() != $this->getParameter('exceptionCheckerSecret')) {
                //Creates flash
                $flash = $translator->trans('text.wrong_secret_code', array(), 'exceptionChecker');
                $request->getSession()
                    ->getFlashBag()
                    ->add('danger', $flash);
            //Access is denied
            } else {
                throw $this->createAccessDeniedException();
            }
        }

        //Returns the form to add url
        return $this->render('@c975LExceptionChecker/forms/new.html.twig', array(
            'form' => $form->createView(),
        ));
    }

//MODIFY
    /**
     * @Route("/exception-checker/modify/{id}",
     *      name="exceptionchecker_modify",
     *      requirements={
     *          "id": "^([0-9]+)"
     *      })
     * @Method({"GET", "HEAD", "POST"})
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

            //Returns the form to modify content
            return $this->render('@c975LExceptionChecker/forms/modify.html.twig', array(
                'form' => $form->createView(),
                'exceptionChecker' => $exceptionChecker,
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

            //Returns the form to duplicate content
            return $this->render('@c975LExceptionChecker/forms/duplicate.html.twig', array(
                'form' => $form->createView(),
                'exceptionChecker' => $exceptionCheckerClone,
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
     * @Method({"GET", "HEAD", "POST"})
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

            //Returns the form to delete content
            return $this->render('@c975LExceptionChecker/forms/delete.html.twig', array(
                'form' => $form->createView(),
                'exceptionChecker' => $exceptionChecker,
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
        //Returns the help
        if ($this->getUser() !== null && $this->get('security.authorization_checker')->isGranted($this->getParameter('c975_l_exception_checker.roleNeeded'))) {
            return $this->render('@c975LExceptionChecker/pages/help.html.twig');
        }

        //Access is denied
        throw $this->createAccessDeniedException();
    }
}