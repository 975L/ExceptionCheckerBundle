<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Service;

use c975L\ExceptionCheckerBundle\Entity\ExceptionChecker;
use Symfony\Component\Form\Form;

/**
 * Interface to be called for DI for ExceptionChecker Main related services
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
interface ExceptionCheckerServiceInterface
{
    /**
     * Clones the object
     * @return ExceptionChecker
     */
    public function cloneObject(ExceptionChecker $exceptionChecker);

    /**
     * Shortcut to call ExceptionCheckerFormFactory to create Form
     * @return Form
     */
    public function createForm(string $name, ExceptionChecker $exceptionChecker, $user);

    /**
     * Deletes the ExceptionChecker
     */
    public function delete(ExceptionChecker $exceptionChecker);

    /**
     * Gets all the ExceptionChecker
     * @return array
     */
    public function getExceptionCheckerAll();

    /**
     * Registers the ExceptionChecker
     */
    public function register(ExceptionChecker $exceptionChecker);

    /**
     * Registers the ExceptionChecker via Url
     * @return bool
     */
    public function registerViaUrl(ExceptionChecker $exceptionChecker, Form $form);
}
