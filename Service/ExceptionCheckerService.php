<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Service;

use c975L\ConfigBundle\Service\ConfigServiceInterface;
use c975L\ExceptionCheckerBundle\Entity\ExceptionChecker;
use c975L\ExceptionCheckerBundle\Form\ExceptionCheckerFormFactoryInterface;
use c975L\ServicesBundle\Service\ServiceToolsInterface;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * Main services related to ExceptionChecker
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2017 975L <contact@975l.com>
 */
class ExceptionCheckerService implements ExceptionCheckerServiceInterface
{
    /**
     * Stores AuthorizationCheckerInterface
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

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
     * Stores ExceptionCheckerFormFactoryInterface
     * @var ExceptionCheckerFormFactoryInterface
     */
    private $exceptionCheckerFormFactory;

    /**
     * Stores ServiceToolsInterface
     * @var ServiceToolsInterface
     */
    private $serviceTools;

    public function __construct(
        AuthorizationCheckerInterface $authChecker,
        ConfigServiceInterface $configService,
        EntityManagerInterface $em,
        ExceptionCheckerFormFactoryInterface $exceptionCheckerFormFactory,
        ServiceToolsInterface $serviceTools
    )
    {
        $this->authChecker = $authChecker;
        $this->configService = $configService;
        $this->em = $em;
        $this->exceptionCheckerFormFactory = $exceptionCheckerFormFactory;
        $this->serviceTools = $serviceTools;
    }

    /**
     * {@inheritdoc}
     */
    public function cloneObject(ExceptionChecker $exceptionChecker)
    {
        $exceptionCheckerClone = clone $exceptionChecker;

        return $exceptionCheckerClone;
    }

    /**
     * {@inheritdoc}
     */
    public function createForm(string $name, ExceptionChecker $exceptionChecker, $user)
    {
        return $this->exceptionCheckerFormFactory->create($name, $exceptionChecker, $user);
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ExceptionChecker $exceptionChecker)
    {
        //Persists data in DB
        $this->em->remove($exceptionChecker);
        $this->em->flush();

        //Creates flash
        $this->serviceTools->createFlash('exceptionChecker', 'text.exception_checker_deleted');
    }

    /**
     * {@inheritdoc}
     */
    public function getExceptionCheckerAll()
    {
        return $this->em
            ->getRepository('c975LExceptionCheckerBundle:ExceptionChecker')
            ->findAll()
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function register(ExceptionChecker $exceptionChecker)
    {
        //Checks if ExceptionChecker already exists
        $existingExceptionChecker = $this->em
            ->getRepository('c975LExceptionCheckerBundle:ExceptionChecker')
            ->findOneByUrl($exceptionChecker->getUrl())
        ;

        //Adds data if ExceptionChecker doesn't exist
        if (null === $existingExceptionChecker) {
            //Adds data
            $exceptionChecker->setCreation(new DateTime());
            if ('' == $exceptionChecker->getRedirectKind()) {
                $exceptionChecker->setRedirectKind(null);
                $exceptionChecker->setRedirectData(null);
            }

            //Persists data in DB
            $this->em->persist($exceptionChecker);
            $this->em->flush();
        }

        //Creates flash
        $this->serviceTools->createFlash('exceptionChecker', 'text.exception_checker_added', 'success', array('%url%' => $exceptionChecker->getUrl()));
    }

    /**
     * {@inheritdoc}
     */
    public function registerViaUrl(ExceptionChecker $exceptionChecker, Form $form)
    {
        //Checks User rights
        if ($this->authChecker->isGranted($this->configService->getParameter('c975LExceptionChecker.roleNeeded')) ||
            $form->get('secret')->getData() == $this->configService->getParameter('c975LExceptionChecker.exceptionCheckerSecret')) {
            //Registers the ExceptionChecker
            $this->register($exceptionChecker);

            return true;
        //Wrong secret code
        } elseif ($form->get('secret')->getData() != $this->configService->getParameter('c975LExceptionChecker.exceptionCheckerSecret')) {
            $this->serviceTools->createFlash('exceptionChecker', 'text.wrong_secret_code', 'danger');
        }

        return false;
    }
}
