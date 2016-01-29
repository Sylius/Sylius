<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShippingMethod;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\FormRegistryInterface;

class BuildShippingMethodFormSubscriberSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $calculatorRegistry, FormFactoryInterface $factory, FormRegistryInterface $formRegistry)
    {
        $this->beConstructedWith($calculatorRegistry, $factory, $formRegistry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\EventListener\BuildShippingMethodFormSubscriber');
    }

    function it_is_a_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn([
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT => 'preSubmit',
        ]);
    }

    function it_adds_configuration_field_on_pre_set_data(
        $calculatorRegistry,
        $factory,
        $formRegistry,
        FormEvent $event,
        FormInterface $form,
        ShippingMethod $shippingMethod,
        FormInterface $formConfiguration,
        CalculatorInterface $calculator
    ) {
        $event->getData()->shouldBeCalled()->willReturn($shippingMethod);
        $event->getForm()->shouldBeCalled()->willReturn($form);

        $shippingMethod->getId()->shouldBeCalled()->willreturn(12);
        $shippingMethod->getCalculator()->shouldBeCalled()->willreturn('calculator_type');
        $shippingMethod->getConfiguration()->shouldBeCalled()->willreturn([]);

        $calculatorRegistry->get('calculator_type')->shouldBeCalled()->willReturn($calculator);
        $calculator->getType()->shouldBeCalled()->willReturn('calculator_type');

        $formRegistry->hasType('sylius_shipping_calculator_calculator_type')->shouldBeCalled()->willReturn(true);

        $factory->createNamed(
            'configuration',
            'sylius_shipping_calculator_calculator_type',
            [],
            ['auto_initialize' => false]
        )->shouldBeCalled()->willReturn($formConfiguration);

        $form->add($formConfiguration)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_adds_configuration_field_on_post_submit(
        $calculatorRegistry,
        $factory,
        $formRegistry,
        FormEvent $event,
        FormInterface $form,
        FormInterface $formConfiguration,
        CalculatorInterface $calculator
    ) {
        $event->getData()->shouldBeCalled()->willReturn(['calculator' => 'calculator_type']);
        $event->getForm()->shouldBeCalled()->willReturn($form);

        $calculatorRegistry->get('calculator_type')->shouldBeCalled()->willReturn($calculator);
        $calculator->getType()->shouldBeCalled()->willReturn('calculator_type');

        $formRegistry->hasType('sylius_shipping_calculator_calculator_type')->shouldBeCalled()->willReturn(true);

        $factory->createNamed(
            'configuration',
            'sylius_shipping_calculator_calculator_type',
            [],
            ['auto_initialize' => false]
        )->shouldBeCalled()->willReturn($formConfiguration);

        $form->add($formConfiguration)->shouldBeCalled();

        $this->preSubmit($event);
    }
}
