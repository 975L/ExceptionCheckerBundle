<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Controller;

use c975L\ConfigBundle\Service\ConfigServiceInterface;
use c975L\ExceptionCheckerBundle\Entity\ExceptionChecker;
use c975L\ExceptionCheckerBundle\Service\ExceptionCheckerServiceInterface;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Main Controller class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class ExceptionCheckerController extends AbstractController
{
    public function __construct(
        /**
         * Stores ExceptionCheckerService
         */
        private readonly ExceptionCheckerServiceInterface $exceptionCheckerService
    )
    {
    }

//DASHBOARD
    /**
     * Displays the dashboard
     * @return Response
     * @throws AccessDeniedException
     */
    #[Route(
        '/exception-checker/dashboard',
        name: 'exceptionchecker_dashboard',
        methods: ['GET']
    )]
    public function dashboard(Request $request, PaginatorInterface $paginator)
    {
        $this->denyAccessUnlessGranted('c975LExceptionChecker-dashboard', null);

        //Renders the dashboard
        $exceptionCheckers = $paginator->paginate(
            $this->exceptionCheckerService->getExceptionCheckerAll(),
            $request->query->getInt('p', 1),
            $request->query->getInt('s', 25)
        );
        return $this->render(
            '@c975LExceptionChecker/pages/dashboard.html.twig',
            ['exceptionCheckers' => $exceptionCheckers]
        )->setMaxAge(3600);
    }

//DISPLAY
    /**
     * Displays the ExceptionChecker using its unique id
     * @return Response
     * @throws NotFoundHttpException
     */
    #[Route(
        '/exception-checker/{id}',
        name: 'exceptionchecker_display',
        requirements: ['id' => '^([0-9]+)'],
        methods: ['GET']
    )]
    public function display(ExceptionChecker $exceptionChecker)
    {
        $this->denyAccessUnlessGranted('c975LExceptionChecker-display', $exceptionChecker);

        //Renders the ExceptionChecker
        return $this->render(
            '@c975LExceptionChecker/pages/display.html.twig',
            ['exceptionChecker' => $exceptionChecker]
        )->setMaxAge(3600);
    }

//CREATE
    /**
     * Creates the ExceptionChecker
     * @return Response
     * @throws AccessDeniedException
     */
    #[Route(
        '/exception-checker/create',
        name: 'exceptionchecker_create',
        methods: ['GET', 'POST']
    )]
    public function create(Request $request)
    {
        $exceptionChecker = new ExceptionChecker();
        $this->denyAccessUnlessGranted('c975LExceptionChecker-create', $exceptionChecker);

        //Defines form
        $form = $this->exceptionCheckerService->createForm('create', $exceptionChecker, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Registers the ExceptionChecker
            $this->exceptionCheckerService->register($exceptionChecker);

            //Redirects to the ExceptionChecker
            return $this->redirectToRoute(
                'exceptionchecker_display',
                ['id' => $exceptionChecker->getId()]);
        }

        //Renders the new form
        return $this->render(
            '@c975LExceptionChecker/forms/create.html.twig',
            ['form' => $form->createView()]
        )->setMaxAge(3600);
    }

//ADD FROM URL CALL
    /**
     * Creates the ExceptionChecker from url call (mainly from link sent in email built with Monolog)
     * @return Response
     * @throws AccessDeniedException
     */
    #[Route(
        '/ec-add/{kind}',
        name: 'exceptionchecker_create_from_url',
        requirements: ['kind' => 'deleted|excluded|ignored|redirected'],
        methods: ['GET', 'POST']
    )]
    public function addFromUrl(Request $request, ConfigServiceInterface $configService, $kind)
    {
        $exceptionChecker = new ExceptionChecker();
        $exceptionChecker
            ->setKind($kind)
            ->setUrl($request->get('u'))
        ;

        //Defines form
        $form = $this->exceptionCheckerService->createForm('add', $exceptionChecker, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Registers the ExceptionChecker
            if ($this->exceptionCheckerService->registerViaUrl($exceptionChecker, $form)) {
                return $this->redirectToRoute($configService->getParameter('c975LExceptionChecker.redirectExcluded'));
            }

            //Access is denied
            throw $this->createAccessDeniedException();
        }

        //Renders the add form
        return $this->render(
            '@c975LExceptionChecker/forms/create.html.twig',
            ['form' => $form->createView()]
        )->setMaxAge(3600);
    }

//MODIFY
    /**
     * Modifies the ExceptionChecker using its unique id
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    #[Route(
        '/exception-checker/modify/{id}',
        name: 'exceptionchecker_modify',
        requirements: ['id' => '^([0-9]+)'],
        methods: ['GET', 'POST']
    )]
    public function modify(Request $request, ExceptionChecker $exceptionChecker)
    {
        $this->denyAccessUnlessGranted('c975LExceptionChecker-modify', $exceptionChecker);

        //Defines form
        $form = $this->exceptionCheckerService->createForm('modify', $exceptionChecker, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Registers the ExceptionChecker
            $this->exceptionCheckerService->register($exceptionChecker);

            //Redirects to the exceptionChecker
            return $this->redirectToRoute(
                'exceptionchecker_display',
                ['id' => $exceptionChecker->getId()]);
        }

        //Renders the modify form
        return $this->render(
            '@c975LExceptionChecker/forms/modify.html.twig',
            ['form' => $form->createView(), 'exceptionChecker' => $exceptionChecker]
        )->setMaxAge(3600);
    }

//DUPLICATE
    /**
     * Duplicates the ExceptionChecker using its unique id
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    #[Route(
        '/exception-checker/duplicate/{id}',
        name: 'exceptionchecker_duplicate',
        requirements: ['id' => '^[0-9]+$'],
        methods: ['GET', 'POST']
    )]
    public function duplicate(Request $request, ExceptionChecker $exceptionChecker)
    {
        $this->denyAccessUnlessGranted('c975LExceptionChecker-duplicate', $exceptionChecker);

        //Defines form
        $exceptionCheckerClone = $this->exceptionCheckerService->cloneObject($exceptionChecker);
        $form = $this->exceptionCheckerService->createForm('duplicate', $exceptionCheckerClone, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Registers the ExceptionChecker
            $this->exceptionCheckerService->register($exceptionCheckerClone);

            //Redirects to the exceptionChecker
            return $this->redirectToRoute(
                'exceptionchecker_display',
                ['id' => $exceptionCheckerClone->getId()]);
        }

        //Renders the duplicate  form
        return $this->render(
            '@c975LExceptionChecker/forms/duplicate.html.twig',
            ['form' => $form->createView(), 'exceptionChecker' => $exceptionCheckerClone]
        )->setMaxAge(3600);
    }

//DELETE
    /**
     * Deletes the ExceptionChecker using its unique id
     * @return Response
     * @throws AccessDeniedException
     * @throws NotFoundHttpException
     */
    #[Route(
        '/exception-checker/delete/{id}',
        name: 'exceptionchecker_delete',
        requirements: ['id' => '^[0-9]+$'],
        methods: ['GET', 'POST']
    )]
    public function delete(Request $request, ExceptionChecker $exceptionChecker)
    {
        $this->denyAccessUnlessGranted('c975LExceptionChecker-delete', $exceptionChecker);

        //Defines form
        $form = $this->exceptionCheckerService->createForm('delete', $exceptionChecker, $this->getUser());
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Deletes the ExceptionChecker
            $this->exceptionCheckerService->delete($exceptionChecker);

            //Redirects to the dashboard
            return $this->redirectToRoute('exceptionchecker_dashboard');
        }

        //Renders the delete form
        return $this->render(
            '@c975LExceptionChecker/forms/delete.html.twig',
            ['form' => $form->createView(), 'exceptionChecker' => $exceptionChecker]
        )->setMaxAge(3600);
    }

//CONFIG
    /**
     * Displays the configuration
     * @return Response
     * @throws AccessDeniedException
     */
    #[Route(
        '/exception-checker/config',
        name: 'exceptionchecker_config',
        methods: ['GET', 'POST']
    )]
    public function config(Request $request, ConfigServiceInterface $configService)
    {
        $this->denyAccessUnlessGranted('c975LExceptionChecker-config', null);

        //Defines form
        $form = $configService->createForm('c975l/exceptionchecker-bundle');
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //Validates config
            $configService->setConfig($form);

            //Redirects
            return $this->redirectToRoute('exceptionchecker_dashboard');
        }

        //Renders the config form
        return $this->render(
            '@c975LConfig/forms/config.html.twig',
            ['form' => $form->createView(), 'toolbar' => '@c975LExceptionChecker']
        )->setMaxAge(3600);
    }

//HELP
    /**
     * Displays the help
     * @return Response
     * @throws AccessDeniedException
     */
    #[Route(
        '/exception-checker/help',
        name: 'exceptionchecker_help',
        methods: ['GET']
    )]
    public function help()
    {
        $this->denyAccessUnlessGranted('c975LExceptionChecker-help', null);

        //Renders the help
        return $this->render('@c975LExceptionChecker/pages/help.html.twig')->setMaxAge(3600);
    }
}
