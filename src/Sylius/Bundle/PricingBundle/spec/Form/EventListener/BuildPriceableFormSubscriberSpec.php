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
use Sylius\Component\Pricing\Calculator\Calculators;
use Sylius\Component\Pricing\Model\PriceableInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormFactoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BuildPriceableFormSubscriberSpec extends ObjectBehavior
{
    function let(
        ServiceRegistryInterface $calculatorRegistry,
        FormFactoryInterface $factory
    ) {
        $this->beConstructedWith($calculatorRegistry, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PricingBundle\Form\EventListener\BuildPriceableFormSubscriber');
    }

    function it_should_be_event_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_should_add_configuration_fields_in_pre_set_data(
        $calculatorRegistry,
        CalculatorInterface $calculator,
        $factory,
        FormEvent $event,
        PriceableInterface $priceable,
        Form $form,
        Form $field
    ) {
        $event->getData()->willReturn($priceable);
        $event->getForm()->willReturn($form);

        $calculator->getType()->willReturn('foo');
        $calculatorRegistry->get('bar')->willReturn($calculator);

        $priceable->getPricingCalculator()->willReturn('bar');
        $priceable->getPricingConfiguration()->willReturn([]);

        $factory->createNamed('pricingConfiguration', 'sylius_price_calculator_foo', [], Argument::any())->willReturn($field);
        $form->add($field)->shouldBeCalled();

        $this->preSetData($event);
    }

    function it_should_set_default_pricing_calculator_to_standard_if_null_on_post_submit(
        FormEvent $event,
        PriceableInterface $priceable
    ) {
        $event->getData()->willReturn($priceable);
        $priceable->getPricingCalculator()->willReturn(null);
        $priceable->setPricingCalculator(Calculators::STANDARD)->shouldBeCalled();

        $this->postSubmit($event);
    }
}
