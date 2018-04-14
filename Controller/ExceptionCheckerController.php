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

class ExceptionCheckerController extends Controller
{
    /**
    * @Route("/excluded",
    *       name="exception_checker_excluded")
    * @Method({"GET", "HEAD"})
    */
    public function excludedAction()
    {
        return $this->render('@c975LExceptionChecker/pages/excluded.html.twig');
    }
}