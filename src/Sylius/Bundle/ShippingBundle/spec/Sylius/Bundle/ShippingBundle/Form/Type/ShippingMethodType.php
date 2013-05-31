<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Form\Type;

use PHPSpec2\ObjectBehavior;

/**
 * Shipping method form type spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingMethodType extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorRegistryInterface $calculatorRegistry
     * @param Sylius\Bundle\ShippingBundle\Checker\Registry\RuleCheckerRegistryInterface   $checkerRegistry
     * @param Symfony\Component\Form\FormBuilder                                           $builder
     * @param Symfony\Component\Form\FormFactoryInterface                                  $factory
     */
    function let($calculatorRegistry, $checkerRegistry, $builder, $factory)
    {
        $this->beConstructedWith('ShippingMethod', $calculatorRegistry, $checkerRegistry);
        $builder->getFormFactory()->willReturn($factory);
    }

    function it_should_be_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_should_build_form_with_proper_fields($builder)
    {
        $builder->addEventSubscriber(ANY_ARGUMENT)->willReturn($builder);
        $builder
            ->add('name', 'text', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('category', 'sylius_shipping_category_choice', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add(
                'categoryRequirement',
                'choice',
                \Mockery::subset(
                    array(
                        'choices' => array(
                            0 => 'None of items have to match method category',
                            1 => 'At least 1 item have to match method category',
                            2 => 'All items have to match method category'
                        )
                    )
                )
            )
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('enabled', 'checkbox', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('calculator', 'sylius_shipping_calculator_choice', ANY_ARGUMENT)
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_should_add_build_shipping_method_event_subscriber($builder)
    {
        $builder->add(ANY_ARGUMENTS)->willReturn($builder);

        $builder
            ->addEventSubscriber(\Mockery::type('Sylius\Bundle\ShippingBundle\Form\EventListener\BuildShippingMethodFormListener'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     * @param Symfony\Component\Form\FormBuilder $flatRateFormBuilder
     * @param Symfony\Component\Form\Form $flatRateForm
     * @param Sylius\Bundle\ShippingBundle\Calculator\FlatRateCalculator $flatRateCalculator
     * @param Symfony\Component\Form\FormBuilder $perItemFormBuilder
     * @param Symfony\Component\Form\Form $perItemForm
     * @param Sylius\Bundle\ShippingBundle\Calculator\PerItemRateCalculator $perItemRateCalculator
     */
    function it_should_build_prototypes_forms_for_calculators(
        $calculatorRegistry, $builder,
        $flatRateFormBuilder, $flatRateForm, $flatRateCalculator,
        $perItemFormBuilder, $perItemForm, $perItemRateCalculator
    )
    {
        $builder
            ->add(ANY_ARGUMENTS)
            ->willReturn($builder)
        ;
        $builder
            ->addEventSubscriber(ANY_ARGUMENT)
            ->willReturn($builder)
        ;

        $flatRateCalculator
            ->getConfigurationFormType()
            ->willReturn('sylius_shipping_calculator_flat_rate_configuration')
        ;
        $perItemRateCalculator
            ->getConfigurationFormType()
            ->willReturn('sylius_shipping_calculator_per_item_rate_configuration')
        ;

        $calculatorRegistry
            ->getCalculators()
            ->willReturn(
                array(
                    'flat_rate'     => $flatRateCalculator,
                    'per_item_rate' => $perItemRateCalculator
                )
            )
            ->shouldBeCalled()
        ;

        $flatRateFormBuilder
            ->getForm()
            ->willReturn($flatRateForm)
        ;
        $builder
            ->create('__name__', 'sylius_shipping_calculator_flat_rate_configuration')
            ->willReturn($flatRateFormBuilder)
        ;

        $perItemFormBuilder
            ->getForm()
            ->willReturn($perItemForm)
        ;
        $builder
            ->create('__name__', 'sylius_shipping_calculator_per_item_rate_configuration')
            ->willReturn($perItemFormBuilder)
        ;

        $builder
            ->setAttribute(
                'prototypes',
                array('calculators' => array(
                    'flat_rate' => $flatRateForm,
                    'per_item_rate' => $perItemForm,
                ))
            )
            ->shouldBeCalled()
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\Form\FormView $formView
     * @param Symfony\Component\Form\FormBuilder $builder
     * @param Symfony\Component\Form\Form $form
     * @param Symfony\Component\Form\Form $flatRateForm
     * @param Symfony\Component\Form\FormView $flatRateFormView
     * @param Symfony\Component\Form\Form $perItemRateForm
     * @param Symfony\Component\Form\FormView $perItemRateFormView
     */
    function it_should_create_form_view_for_calculators_prototypes_when_building_view(
        $formView, $builder, $form, $flatRateForm, $flatRateFormView, $perItemRateForm, $perItemRateFormView
    )
    {
        $flatRateForm
            ->createView($formView)
            ->willReturn($flatRateFormView)
            ->shouldBeCalled()
        ;
        $perItemRateForm
            ->createView($formView)
            ->willReturn($perItemRateFormView)
            ->shouldBeCalled()
        ;

        $builder
            ->getAttribute('prototypes')
            ->willReturn(array(
                'flat_rate'     => $flatRateForm,
                'per_item_rate' => $perItemRateForm
            ))
        ;
        $form->getConfig()->willReturn($builder);

        $this->buildView($formView, $form, array());

        $formView->vars['prototypes']['flat_rate']->shouldBe($flatRateFormView);
        $formView->vars['prototypes']['per_item_rate']->shouldBe($perItemRateFormView);
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_should_define_assigned_data_class($resolver)
    {
        $resolver->setDefaults(array('data_class' => 'ShippingMethod'))->shouldBeCalled();

        $this->setDefaultOptions($resolver);
    }
}
