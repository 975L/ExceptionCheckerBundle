<?php
/*
 * (c) 2018: 975L <contact@975l.com>
 * (c) 2018: Laurent Marquet <laurent.marquet@laposte.net>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace c975L\ExceptionCheckerBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * ExceptionChecker FormType
 * @author Laurent Marquet <laurent.marquet@laposte.net>
 * @copyright 2018 975L <contact@975l.com>
 */
class ExceptionCheckerType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $disabled = $options['config']['action'] == 'delete' ? true : false;
        $user = $options['config']['user'] ?? false;

        $builder
            ->add(
                'url',
                TextType::class,
                [
                    'label' => 'label.url',
                    'disabled' => $disabled,
                    'required' => true,
                    'attr' => [
                        'placeholder' => 'label.url'
                    ]
                ]
            )
            ->add(
                'kind',
                ChoiceType::class,
                [
                    'label' => 'label.kind',
                    'disabled' => $disabled,
                    'required' => true,
                    'choices' => [
                        'label.kind' => '',
                        'label.deleted' => 'deleted',
                        'label.excluded' => 'excluded',
                        'label.ignored' => 'ignored',
                        'label.redirected' => 'redirected'
                    ]
                ]
            )
            ->add(
                'redirectKind',
                ChoiceType::class,
                [
                    'label' => 'label.redirect_kind',
                    'disabled' => $disabled,
                    'required' => false,
                    'choices' => [
                        'label.redirect_kind' => '',
                        'Asset' => 'Asset',
                        'Route' => 'Route',
                        'Url' => 'Url'
                    ],
                    'attr' => [
                        'placeholder' => 'label.redirect_kind'
                    ]
                ]
            )
            ->add(
                'redirectData',
                TextType::class,
                [
                    'label' => 'label.redirect_data',
                    'disabled' => $disabled,
                    'required' => false,
                    'attr' => [
                        'placeholder' => 'label.redirect_data'
                    ]
                ]
            );
        //Creation
        if ($options['config']['action'] == 'modify' || $options['config']['action'] == 'delete') {
            $builder
                ->add(
                    'creation',
                    DateTimeType::class,
                    [
                        'label' => 'label.creation',
                        'disabled' => true,
                        'required' => false,
                        'widget' => 'single_text',
                        'format' => 'dd-MM-yyyy',
                        'html5' => false
                    ]
                );
        }
        //Secret code
        if ($user === false) {
            $builder
                ->add(
                    'secret',
                    TextType::class,
                    [
                        'label' => 'label.secret_code',
                        'mapped' => false,
                        'disabled' => false,
                        'required' => true
                    ]
                );
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => \c975L\ExceptionCheckerBundle\Entity\ExceptionChecker::class,
            'intention'  => 'exceptionCheckerForm',
            'translation_domain' => 'exceptionChecker'
        ]);

        $resolver->setRequired('config');
    }
}
