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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShippingMethodTypeSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorRegistryInterface $calculatorRegistry
     * @param Sylius\Bundle\ShippingBundle\Checker\Registry\RuleCheckerRegistryInterface   $checkerRegistry
     * @param Symfony\Component\Form\FormBuilder                                           $builder
     * @param Symfony\Component\Form\FormFactoryInterface                                  $factory
     */
    function let($calculatorRegistry, $checkerRegistry, $builder, $factory)
    {
        $this->beConstructedWith('ShippingMethod', array('sylius'), $calculatorRegistry, $checkerRegistry);

        $builder->getFormFactory()->willReturn($factory);
        $checkerRegistry->getCheckers()->willReturn(array());
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_builds_form_with_proper_fields($builder, $calculatorRegistry)
    {
        $calculatorRegistry->getCalculators()->willReturn(array());

        $builder->addEventSubscriber(Argument::any())->willReturn($builder);
        $builder
            ->add('name', 'text', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('category', 'sylius_shipping_category_choice', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add(
                'categoryRequirement',
                'choice',
                Argument::type('array')
            )
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('enabled', 'checkbox', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder
            ->add('calculator', 'sylius_shipping_calculator_choice', Argument::any())
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder->setAttribute(Argument::any(), Argument::any())->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\Form\FormBuilder $builder
     */
    function it_adds_build_shipping_method_event_subscriber($builder, $calculatorRegistry)
    {
        $calculatorRegistry->getCalculators()->willReturn(array());
        $builder->add(Argument::any(), Argument::cetera())->willReturn($builder);

        $builder
            ->addEventSubscriber(Argument::type('Sylius\Bundle\ShippingBundle\Form\EventListener\BuildShippingMethodFormListener'))
            ->shouldBeCalled()
            ->willReturn($builder)
        ;

        $builder->setAttribute(Argument::any(), Argument::any())->shouldBeCalled();

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
    function it_builds_prototypes_forms_for_calculators(
        $calculatorRegistry, $builder,
        $flatRateFormBuilder, $flatRateForm, $flatRateCalculator,
        $perItemFormBuilder, $perItemForm, $perItemRateCalculator
    )
    {
        $builder
            ->add(Argument::any(), Argument::cetera())
            ->willReturn($builder)
        ;
        $builder
            ->addEventSubscriber(Argument::any())
            ->willReturn($builder)
        ;

        $flatRateCalculator
            ->getConfigurationFormType()
            ->willReturn('sylius_shipping_calculator_flat_rate_configuration')
        ;
        $flatRateCalculator
            ->isConfigurable()
            ->willReturn(true)
            ->shouldBeCalled()
        ;
        $perItemRateCalculator
            ->getConfigurationFormType()
            ->willReturn('sylius_shipping_calculator_per_item_rate_configuration')
        ;
        $perItemRateCalculator
            ->isConfigurable()
            ->willReturn(true)
            ->shouldBeCalled()
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
            ->create('configuration', 'sylius_shipping_calculator_flat_rate_configuration')
            ->willReturn($flatRateFormBuilder)
        ;

        $perItemFormBuilder
            ->getForm()
            ->willReturn($perItemForm)
        ;
        $builder
            ->create('configuration', 'sylius_shipping_calculator_per_item_rate_configuration')
            ->willReturn($perItemFormBuilder)
        ;

        $builder
            ->setAttribute(
                'prototypes',
                array(
                    'calculators' => array(
                        'flat_rate' => $flatRateForm,
                        'per_item_rate' => $perItemForm,
                    ),
                    'rules' => array()
                )
            )
            ->shouldBeCalled()
        ;

        $this->buildForm($builder, array());
    }

    /**
     * @param Symfony\Component\OptionsResolver\OptionsResolverInterface $resolver
     */
    function it_defines_assigned_data_class($resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => 'ShippingMethod',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled()
        ;

        $this->setDefaultOptions($resolver);
    }
}
