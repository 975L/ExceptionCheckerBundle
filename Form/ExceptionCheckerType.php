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
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExceptionCheckerType extends AbstractType
{
    //Builds the form
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $disabled = $options['exceptionCheckerConfig']['action'] == 'delete' ? true : false;

        $builder
            ->add('url', TextType::class, array(
                'label' => 'label.url',
                'disabled' => $disabled,
                'required' => true,
                'attr' => array(
                    'placeholder' => 'label.url',
                )))
            ->add('kind', ChoiceType::class, array(
                'label' => 'label.kind',
                'disabled' => $disabled,
                'required' => true,
                'choices' => array(
                    'label.kind' => '',
                    'label.deleted' => 'deleted',
                    'label.excluded' => 'excluded',
                    'label.redirected' => 'redirected',
                ),
                ))
            ->add('redirectKind', ChoiceType::class, array(
                'label' => 'label.redirect_kind',
                'disabled' => $disabled,
                'required' => false,
                'choices' => array(
                    'label.redirect_kind' => '',
                    'Asset' => 'Asset',
                    'Route' => 'Route',
                    'Url' => 'Url',
                ),
                'attr' => array(
                    'placeholder' => 'label.redirect_kind',
                )))
            ->add('redirectData', TextType::class, array(
                'label' => 'label.redirect_data',
                'disabled' => $disabled,
                'required' => false,
                'attr' => array(
                    'placeholder' => 'label.redirect_data',
                )))
            ;
            if ($options['exceptionCheckerConfig']['action'] != 'new') {
                $builder
                    ->add('creation', DateTimeType::class, array(
                        'label' => 'label.creation',
                        'disabled' => true,
                        'required' => false,
                        'widget' => 'single_text',
                        'format' => 'dd-MM-yyyy',
                        'html5' => false,
                        ))
                    ;
                }
//Submit
            $submitLabel = $options['exceptionCheckerConfig']['action'] == 'delete' ? 'delete' : 'validate';
            $submitClass = $options['exceptionCheckerConfig']['action'] == 'delete' ? 'btn-danger' : 'btn-primary';
            $builder
                ->add('submit', SubmitType::class, array(
                    'label' => 'label.' . $submitLabel,
                    'translation_domain' => 'toolbar',
                    'attr' => array('class' => 'btn btn-block btn-lg ' . $submitClass),
                ))
            ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'c975L\ExceptionCheckerBundle\Entity\ExceptionChecker',
            'intention'  => 'exceptionCheckerForm',
            'translation_domain' => 'exceptionChecker',
        ));

        $resolver->setRequired('exceptionCheckerConfig');
    }
}