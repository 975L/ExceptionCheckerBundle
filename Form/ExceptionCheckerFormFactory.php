<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Form;

use c975L\ExceptionCheckerBundle\Entity\ExceptionChecker;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * ExceptionCheckerFormFactory class
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class ExceptionCheckerFormFactory implements ExceptionCheckerFormFactoryInterface
{
    /**
     * Stores FormFactoryInterface
     * @var FormFactoryInterface
     */
    private $formFactory;

    public function __construct(FormFactoryInterface $formFactory)
    {
        $this->formFactory = $formFactory;
    }

    /**
     * {@inheritdoc}
     */
    public function create(string $name, ExceptionChecker $exceptionChecker, $user)
    {
        switch ($name) {
            case 'create':
            case 'add':
            case 'modify':
            case 'duplicate':
            case 'delete':
                $config = array(
                    'action' => $name,
                    'user' => $user,
                );
                break;
            default:
                $config = array();
                break;
        }

        return $this->formFactory->create(ExceptionCheckerType::class, $exceptionChecker, array('config' => $config));
    }
}
