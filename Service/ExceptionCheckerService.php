<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use c975L\ExceptionCheckerBundle\Entity\ExceptionChecker;
use c975L\ExceptionCheckerBundle\Service\ExceptionCheckerServiceInterface;
use c975L\ExceptionCheckerBundle\Service\Tools\ExceptionCheckerToolsInterface;

/**
 * Main services related to Events
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2017 975L <contact@975l.com>
 */
class ExceptionCheckerService implements ExceptionCheckerServiceInterface
{
    /**
     * Stores AuthorizationChecker
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * Stores container
     * @var ContainerInterface
     */
    private $container;

    /**
     * Stores EntityManager
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * Stores ExceptionCheckerTools
     * @var ExceptionCheckerToolsInterface
     */
    private $exceptionCheckerTools;

    public function __construct(
        AuthorizationCheckerInterface $authChecker,
        ContainerInterface $container,
        EntityManagerInterface $em,
        ExceptionCheckerToolsInterface $exceptionCheckerTools
    )
    {
        $this->authChecker = $authChecker;
        $this->container = $container;
        $this->em = $em;
        $this->exceptionCheckerTools = $exceptionCheckerTools;
    }

    /**
     * {@inheritdoc}
     */
    public function delete(ExceptionChecker $exceptionChecker)
    {
        //Persists data in DB
        $this->em->remove($exceptionChecker);
        $this->em->flush();
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
            $exceptionChecker->setCreation(new \DateTime());
            if ('' == $exceptionChecker->getRedirectKind()) {
                $exceptionChecker->setRedirectKind(null);
                $exceptionChecker->setRedirectData(null);
            }

            //Persists data in DB
            $this->em->persist($exceptionChecker);
            $this->em->flush();
        }

        //Creates flash
        $this->exceptionCheckerTools->createFlash('exception_checker_added', $exceptionChecker->getUrl());
    }

    /**
     * {@inheritdoc}
     */
    public function registerViaUrl(ExceptionChecker $exceptionChecker, Form $form)
    {
        //Checks User rights
        if (true === $this->authChecker->isGranted($this->container->getParameter('c975_l_exception_checker.roleNeeded')) ||
            $form->get('secret')->getData() == $this->container->getParameter('exceptionCheckerSecret')) {
            //Registers the ExceptionChecker
            $this->register($exceptionChecker);

            return true;
        //Wrong secret code
        } elseif ($form->get('secret')->getData() != $this->getParameter('exceptionCheckerSecret')) {
            $this->exceptionCheckerTools->createFlash('wrong_secret_code');
        }

        return false;
    }
}
