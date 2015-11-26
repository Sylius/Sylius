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
use Sylius\Component\Shipping\Calculator\FlatRateCalculator;
use Sylius\Component\Shipping\Calculator\PerItemRateCalculator;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormBuilder;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormRegistryInterface;
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
        $this->beConstructedWith('ShippingMethod', array('sylius'), $calculatorRegistry, $checkerRegistry, $formRegistry);

        $builder->getFormFactory()->willReturn($factory);
        $checkerRegistry->all()->willReturn(array());
    }

    function it_is_a_form_type()
    {
        $this->shouldImplement('Symfony\Component\Form\FormTypeInterface');
    }

    function it_builds_form_with_proper_fields(FormBuilder $builder, $calculatorRegistry)
    {
        $calculatorRegistry->all()->willReturn(array());

        $builder
            ->addEventSubscriber(Argument::type('Sylius\Bundle\ShippingBundle\Form\EventListener\BuildShippingMethodFormSubscriber'))
            ->willReturn($builder)
        ;

        $builder
            ->addEventSubscriber(Argument::type('Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber'))
            ->willReturn($builder)
        ;

        $builder
            ->add('translations', 'a2lix_translationsForms', Argument::any())
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

        $builder->setAttribute(Argument::any(), Argument::any())->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    function it_adds_build_shipping_method_event_subscriber(
        FormBuilder $builder,
        $calculatorRegistry
    ) {
        $calculatorRegistry->all()->willReturn(array());
        $builder->add(Argument::any(), Argument::cetera())->willReturn($builder);

        $builder
            ->addEventSubscriber(Argument::type('Sylius\Bundle\ShippingBundle\Form\EventListener\BuildShippingMethodFormSubscriber'))
            ->willReturn($builder)
        ;

        $builder
            ->addEventSubscriber(Argument::type('Sylius\Bundle\ResourceBundle\Form\EventSubscriber\AddCodeFormSubscriber'))
            ->willReturn($builder)
        ;

        $builder->setAttribute(Argument::any(), Argument::any())->shouldBeCalled();

        $this->buildForm($builder, array());
    }

    function it_builds_prototypes_forms_for_calculators(
        $calculatorRegistry,
        FormBuilder $builder,
        FormBuilder $flatRateFormBuilder,
        Form $flatRateForm,
        FlatRateCalculator $flatRateCalculator,
        FormBuilder $perItemFormBuilder,
        Form $perItemForm,
        PerItemRateCalculator $perItemRateCalculator,
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

        $perItemRateCalculator
            ->getType()
            ->willReturn('per_item_rate')
        ;

        $calculatorRegistry
            ->all()
            ->willReturn(
                array(
                    'flat_rate'     => $flatRateCalculator,
                    'per_item_rate' => $perItemRateCalculator
                )
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

        $perItemFormBuilder
            ->getForm()
            ->willReturn($perItemForm)
        ;

        $builder
            ->create('configuration', 'sylius_shipping_calculator_per_item_rate')
            ->willReturn($perItemFormBuilder)
        ;

        $formRegistry->hasType('sylius_shipping_calculator_per_item_rate')->shouldBeCalled()->willReturn(true);
        $formRegistry->hasType('sylius_shipping_calculator_flat_rate')->shouldBeCalled()->willReturn(true);

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

    function it_defines_assigned_data_class(OptionsResolver $resolver)
    {
        $resolver
            ->setDefaults(array(
                'data_class'        => 'ShippingMethod',
                'validation_groups' => array('sylius'),
            ))
            ->shouldBeCalled()
        ;

        $this->configureOptions($resolver);
    }
}
