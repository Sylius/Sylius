<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PricingBundle\Form\EventListener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BuildPriceableFormListenerSpec extends ObjectBehavior
{
    function let(
        ServiceRegistryInterface $calculatorRegistry,
        CalculatorInterface $calculator,
        FormFactoryInterface $factory
    )
    {
        $this->beConstructedWith($calculatorRegistry, $factory);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PricingBundle\Form\EventListener\BuildPriceableFormListener');
    }

    function it_should_be_event_subscriber()
    {
        $this->shouldImplement('Symfony\Component\EventDispatcher\EventSubscriberInterface');
    }

    function it_should_add_configuration_fields_in_pre_set_data(
        $calculatorRegistry,
        $calculator,
        $factory,
        FormEvent $event,
        PriceableInterface $priceable,
        Form $form,
        Form $field
    )
    {
        $event->getData()->shouldBeCalled()->willReturn($priceable);
        $event->getForm()->shouldBeCalled()->willReturn($form);

        $calculator->getType()->shouldBeCalled()->willReturn('foo');
        $calculatorRegistry->get('bar')->shouldBeCalled()->willReturn($calculator);

        $priceable->getPricingCalculator()->shouldBeCalled()->willReturn('bar');
        $priceable->getPricingConfiguration()->shouldBeCalled()->willReturn(array());

        $factory->createNamed('pricingConfiguration', 'sylius_price_calculator_foo', array(), Argument::any())->shouldBeCalled()->willReturn($field);
        $form->add($field)->shouldBeCalled();

        $this->preSetData($event);
    }
}
