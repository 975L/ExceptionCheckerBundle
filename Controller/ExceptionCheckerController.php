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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Translation\Translator;
use Knp\Component\Pager\PaginatorInterface;
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
    public function dashboard(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('dashboard', null);

        //Gets the exceptionCheckers
        $exceptionCheckers = $this->getDoctrine()
            ->getManager()
            ->getRepository('c975LExceptionCheckerBundle:ExceptionChecker')
            ->findAll();

        //Pagination
        $pagination = $paginator->paginate(
            $exceptionCheckers,
            $request->query->getInt('p', 1),
            25
        );

        //Renders the dashboard
        return $this->render('@c975LExceptionChecker/pages/dashboard.html.twig', array(
            'exceptionCheckers' => $pagination,
        ));
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
    public function display(ExceptionChecker $exceptionChecker)
    {
        $this->denyAccessUnlessGranted('display', $exceptionChecker);

        //Renders the ExceptionChecker
        return $this->render('@c975LExceptionChecker/pages/display.html.twig', array(
            'exceptionChecker' => $exceptionChecker,
        ));
    }

//ADD
    /**
     * @Route("/exception-checker/new",
     *      name="exceptionchecker_add")
     * @Method({"GET", "HEAD", "POST"})
     */
    public function add(Request $request)
    {
        $exceptionChecker = new ExceptionChecker();
        $this->denyAccessUnlessGranted('add', $exceptionChecker);

        //Defines form
        $exceptionCheckerConfig = array(
            'action' => 'new',
            'user' => $this->getUser(),
        );
        $form = $this->createForm(ExceptionCheckerType::class, $exceptionChecker, array('exceptionCheckerConfig' => $exceptionCheckerConfig));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Adds data
            $exceptionChecker->setCreation(new \DateTime());
            if ('' == $exceptionChecker->getRedirectKind()) {
                $exceptionChecker->setRedirectKind(null);
            }

            //Persists data in DB
            $em = $this->getDoctrine()->getManager();
            $em->persist($exceptionChecker);
            $em->flush();

            //Redirects to the exceptionChecker
            return $this->redirectToRoute('exceptionchecker_display', array(
                'id' => $exceptionChecker->getId(),
            ));
        }

        //Renders the new form
        return $this->render('@c975LExceptionChecker/forms/add.html.twig', array(
            'form' => $form->createView(),
        ));
    }

//ADD FROM URL CALL
    /**
     * @Route("/ec-add/{kind}",
     *      name="exceptionchecker_add_from_url",
     *      requirements={
     *          "kind": "deleted|excluded"
     *      })
     * @Method({"GET", "HEAD", "POST"})
     */
    public function addFromUrl(Request $request, Translator $translator, AuthorizationCheckerInterface $authChecker, $kind)
    {
        //Defines form
        $exceptionChecker = new ExceptionChecker();
        $exceptionChecker
            ->setKind($kind)
            ->setUrl($request->get('u'))
        ;
        $exceptionCheckerConfig = array(
            'action' => 'add',
            'user' => $this->getUser(),
        );
        $form = $this->createForm(ExceptionCheckerType::class, $exceptionChecker, array('exceptionCheckerConfig' => $exceptionCheckerConfig));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Adds url if user has rights
            if (true === $authChecker->isGranted($this->getParameter('c975_l_exception_checker.roleNeeded')) ||
                $form->get('secret')->getData() == $this->getParameter('exceptionCheckerSecret')) {
                //Checks if exceptionChecker already exists
                $em = $this->getDoctrine()->getManager();
                $existingExceptionChecker = $em->getRepository('c975LExceptionCheckerBundle:ExceptionChecker')->findOneByUrl($request->get('u'));

                //ExceptionChecker url doesn't exist
                if (null === $existingExceptionChecker) {
                    //Adds data
                    $exceptionChecker->setCreation(new \DateTime());

                    //Persists data in DB
                    $em->persist($exceptionChecker);
                    $em->flush();
                }

                //Creates flash
                $request->getSession()
                    ->getFlashBag()
                    ->add('success', $translator->trans('text.exception_checker_added', array('%url%' => $exceptionChecker->getUrl()), 'exceptionChecker'));

                //Redirects to the ExceptionChecker
                return $this->redirectToRoute($this->getParameter('c975_l_exception_checker.redirectExcluded'));
            //Wrong secret code
            } elseif ($form->get('secret')->getData() != $this->getParameter('exceptionCheckerSecret')) {
                //Creates flash
                $request->getSession()
                    ->getFlashBag()
                    ->add('danger', $translator->trans('text.wrong_secret_code', array(), 'exceptionChecker'));
            }

            //Access is denied
            throw $this->createAccessDeniedException();
        }

        //Renders the add form
        return $this->render('@c975LExceptionChecker/forms/add.html.twig', array(
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
    public function modify(Request $request, ExceptionChecker $exceptionChecker)
    {
        $this->denyAccessUnlessGranted('modify', $exceptionChecker);

        //Defines form
        $exceptionCheckerConfig = array(
            'action' => 'modify',
            'user' => $this->getUser(),
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($exceptionChecker);
            $em->flush();

            //Redirects to the exceptionChecker
            return $this->redirectToRoute('exceptionchecker_display', array(
                'id' => $exceptionChecker->getId(),
            ));
        }

        //Renders the modify form
        return $this->render('@c975LExceptionChecker/forms/modify.html.twig', array(
            'form' => $form->createView(),
            'exceptionChecker' => $exceptionChecker,
        ));
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
    public function duplicate(Request $request, ExceptionChecker $exceptionChecker)
    {
        $this->denyAccessUnlessGranted('duplicate', $exceptionChecker);

        //Defines form
        $exceptionCheckerClone = clone $exceptionChecker;
        $exceptionCheckerConfig = array(
            'action' => 'duplicate',
            'user' => $this->getUser(),
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
            $em = $this->getDoctrine()->getManager();
            $em->persist($exceptionCheckerClone);
            $em->flush();

            //Redirects to the exceptionChecker
            return $this->redirectToRoute('exceptionchecker_display', array(
                'id' => $exceptionChecker->getId(),
            ));
        }

        //Renders the duplicate  form
        return $this->render('@c975LExceptionChecker/forms/duplicate.html.twig', array(
            'form' => $form->createView(),
            'exceptionChecker' => $exceptionCheckerClone,
        ));
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
    public function delete(Request $request, ExceptionChecker $exceptionChecker)
    {
        $this->denyAccessUnlessGranted('delete', $exceptionChecker);

        //Defines form
        $exceptionCheckerConfig = array(
            'action' => 'delete',
            'user' => $this->getUser(),
        );
        $form = $this->createForm(ExceptionCheckerType::class, $exceptionChecker, array('exceptionCheckerConfig' => $exceptionCheckerConfig));
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Persists data in DB
            $em = $this->getDoctrine()->getManager();
            $em->remove($exceptionChecker);
            $em->flush();

            //Redirects to the dashboard
            return $this->redirectToRoute('exceptionchecker_dashboard');
        }

        //Renders the delete form
        return $this->render('@c975LExceptionChecker/forms/delete.html.twig', array(
            'form' => $form->createView(),
            'exceptionChecker' => $exceptionChecker,
        ));
    }

//HELP
    /**
     * @Route("/exception-checker/help",
     *      name="exceptionchecker_help")
     * @Method({"GET", "HEAD"})
     */
    public function help()
    {
        $this->denyAccessUnlessGranted('help', null);

        //Renders the help
        return $this->render('@c975LExceptionChecker/pages/help.html.twig');
    }
}
