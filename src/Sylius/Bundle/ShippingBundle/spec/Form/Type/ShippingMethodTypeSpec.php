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
use Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber;
use Sylius\Bundle\ShippingBundle\Form\EventListener\BuildShippingMethodFormSubscriber;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\FlatRateCalculator;
use Sylius\Component\Shipping\Calculator\PerUnitRateCalculator;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistryInterface;
use Symfony\Component\Form\FormTypeInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class ShippingMethodTypeSpec extends ObjectBehavior
{
    function let(
        ServiceRegistryInterface $calculatorRegistry,
        ServiceRegistryInterface $checkerRegistry,
        FormRegistryInterface $formRegistry,
        FormBuilder $builder,
        FormFactoryInterface $factory
    ) {
        $this->beConstructedWith('ShippingMethod', ['sylius'], $calculatorRegistry, $checkerRegistry, $formRegistry);

        $builder->getFormFactory()->willReturn($factory);
        $checkerRegistry->all()->willReturn([]);
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement(FormTypeInterface::class);
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder, $calculatorRegistry)
    {
        $calculatorRegistry->all()->willReturn([]);

        $builder
            ->addEventSubscriber(Argument::type(BuildShippingMethodFormSubscriber::class))
            ->willReturn($builder)
        ;

        $builder
            ->addEventSubscriber(Argument::type(AddCodeFormSubscriber::class))
            ->willReturn($builder)
        ;

        $builder
            ->add('translations', 'sylius_translations', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('category', 'sylius_shipping_category_choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('categoryRequirement', 'choice', Argument::type('array'))
            ->willReturn($builder)
        ;

        $builder
            ->add('calculator', 'sylius_shipping_calculator_choice', Argument::any())
            ->willReturn($builder)
        ;

        $builder
            ->add('enabled', 'checkbox', Argument::any())
            ->willReturn($builder)
        ;

        $builder->setAttribute(Argument::any(), Argument::any())->shouldBeCalled();

        $this->buildForm($builder, []);
    }

    function it_adds_build_shipping_method_event_subscriber(
        FormBuilder $builder,
        $calculatorRegistry
    ) {
        $calculatorRegistry->all()->willReturn([]);
        $builder->add(Argument::any(), Argument::cetera())->willReturn($builder);

        $builder
            ->addEventSubscriber(Argument::type(BuildShippingMethodFormSubscriber::class))
            ->willReturn($builder)
        ;

        $builder
            ->addEventSubscriber(Argument::type(AddCodeFormSubscriber::class))
            ->willReturn($builder)
        ;

        $builder->setAttribute(Argument::any(), Argument::any())->shouldBeCalled();

        $this->buildForm($builder, []);
    }

    function it_builds_prototypes_forms_for_calculators(
        $calculatorRegistry,
        FormBuilder $builder,
        FormBuilder $flatRateFormBuilder,
        Form $flatRateForm,
        FlatRateCalculator $flatRateCalculator,
        FormBuilder $perUnitFormBuilder,
        Form $perUnitForm,
        PerUnitRateCalculator $perUnitRateCalculator,
        $formRegistry
    ) {
        $builder
            ->add(Argument::any(), Argument::cetera())
            ->willReturn($builder)
        ;

        $builder
            ->addEventSubscriber(Argument::any())
            ->willReturn($builder)
        ;

        $flatRateCalculator
            ->getType()
            ->willReturn('flat_rate')
        ;

        $perUnitRateCalculator
            ->getType()
            ->willReturn('per_unit_rate')
        ;

        $calculatorRegistry
            ->all()
            ->willReturn(
                [
                    'flat_rate' => $flatRateCalculator,
                    'per_unit_rate' => $perUnitRateCalculator,
                ]
            )
        ;

        $flatRateFormBuilder
            ->getForm()
            ->willReturn($flatRateForm)
        ;

        $builder
            ->create('configuration', 'sylius_shipping_calculator_flat_rate')
            ->willReturn($flatRateFormBuilder)
        ;

        $perUnitFormBuilder
            ->getForm()
            ->willReturn($perUnitForm)
        ;

        $builder
            ->create('configuration', 'sylius_shipping_calculator_per_unit_rate')
            ->willReturn($perUnitFormBuilder)
        ;

        $formRegistry->hasType('sylius_shipping_calculator_per_unit_rate')->shouldBeCalled()->willReturn(true);
        $formRegistry->hasType('sylius_shipping_calculator_flat_rate')->shouldBeCalled()->willReturn(true);

        $builder
            ->setAttribute(
                'prototypes',
                [
                    'calculators' => [
                        'flat_rate' => $flatRateForm,
                        'per_unit_rate' => $perUnitForm,
                    ],
                    'rules' => [],
                ]
            )
            ->shouldBeCalled()
        ;

        $this->buildForm($builder, []);
    }

    function it_defines_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults([
                'data_class' => 'ShippingMethod',
                'validation_groups' => ['sylius'],
            ])
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }
}
