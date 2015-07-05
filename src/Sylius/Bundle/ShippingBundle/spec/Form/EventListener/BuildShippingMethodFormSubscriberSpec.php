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
use Prophecy\Argument;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Calculator\Registry\CalculatorRegistryInterface;
use Sylius\Component\Shipping\Model\ShippingMethod;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormFactoryInterface;
use Symfony\Component\Form\FormInterface;

class BuildShippingMethodFormSubscriberSpec extends ObjectBehavior
{
    function let(CalculatorRegistryInterface $calculatorRegistry, FormFactoryInterface $factory)
    {
        $this->beConstructedWith($calculatorRegistry, $factory);
    }
    
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Form\EventListener\BuildShippingMethodFormSubscriber');
    }

    function it_is_a_subscriber()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn(array(
            FormEvents::PRE_SET_DATA => 'preSetData',
            FormEvents::PRE_SUBMIT   => 'preSubmit'
        ));
    }

    function it_adds_configuration_field_on_pre_set_data(
        $calculatorRegistry,
        $factory,
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
        $shippingMethod->getConfiguration()->shouldBeCalled()->willreturn(array());

        $calculatorRegistry->getCalculator('calculator_type')->shouldBeCalled()->willreturn($calculator);
        $calculator->getConfigurationFormType()->shouldBeCalled()->willreturn('configuration_form_type');
        $calculator->isConfigurable()->shouldBeCalled()->willreturn(true);

        $factory->createNamed(
            'configuration',
            'configuration_form_type',
            array(),
            array('auto_initialize' => false)
        )->shouldBeCalled()->willreturn($formConfiguration);

        $form->add($formConfiguration)->shouldBeCalled();
        
        $this->preSetData($event);
    }

    function it_adds_configuration_field_on_post_submit(
        $calculatorRegistry,
        $factory,
        FormEvent $event,
        FormInterface $form,
        ShippingMethodInterface $shippingMethod,
        FormInterface $formConfiguration,
        CalculatorInterface $calculator
    ) {
        $event->getData()->shouldBeCalled()->willReturn(array('calculator' => 'calculator_type'));
        $event->getForm()->shouldBeCalled()->willReturn($form);

        $calculatorRegistry->getCalculator('calculator_type')->shouldBeCalled()->willreturn($calculator);
        $calculator->getConfigurationFormType()->shouldBeCalled()->willreturn('configuration_form_type');
        $calculator->isConfigurable()->shouldBeCalled()->willreturn(true);

        $factory->createNamed(
            'configuration',
            'configuration_form_type',
            array(),
            array('auto_initialize' => false)
        )->shouldBeCalled()->willreturn($formConfiguration);

        $form->add($formConfiguration)->shouldBeCalled();

        $this->preSubmit($event);
    }
}
