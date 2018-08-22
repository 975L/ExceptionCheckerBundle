<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Service\Tools;

use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Translation\TranslatorInterface;
use c975L\ExceptionCheckerBundle\Service\Tools\ExceptionCheckerToolsInterface;

/**
 * Services related to ExceptionChecker Tools
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class ExceptionCheckerTools implements ExceptionCheckerToolsInterface
{
    /**
     * Stores current Request
     * @var RequestStack
     */
    private $request;

    /**
     * Stores Translator
     * @var TranslatorInterface
     */
    private $translator;

    public function __construct(
        RequestStack $requestStack,
        TranslatorInterface $translator
    )
    {
        $this->request = $requestStack->getCurrentRequest();
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function createFlash($object, $url = null)
    {
        $style = 'success';
        $options = array();

        switch ($object) {
            //ExceptionChecker added
            case 'exception_checker_added':
                $flash = 'text.exception_checker_added';
                $options = array('%url%' => $url);
                break;
            //Wrong secret code
            case 'wrong_secret_code':
                $flash = 'text.wrong_secret_code';
                $style = 'danger';
                break;
        }

        if(isset($flash)) {
            $this->request->getSession()
                ->getFlashBag()
                ->add($style, $this->translator->trans($flash, $options, 'exceptionChecker'))
            ;
        }
    }
}
