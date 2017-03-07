<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Shipping\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Core\Exception\ChannelNotDefinedException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Core\Shipping\Calculator\PerUnitRateCalculator;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class PerUnitRateCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PerUnitRateCalculator::class);
    }

    function it_implements_shipping_calculator_interface()
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_returns_per_unit_rate_type(CalculatorInterface $calculator)
    {
        $calculator->getType()->willReturn('per_unit_rate');

        $this->getType()->shouldReturn('per_unit_rate');
    }

    function it_calculates_the_total_with_the_per_unit_amount_configured_on_the_method(
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');
        $shipment->getShippingUnitCount()->willReturn(10);

        $this->calculate($shipment, ['WEB' => ['amount' => 200]])->shouldReturn(2000);
    }

    function it_throws_a_channel_not_defined_exception_if_channel_code_key_does_not_exist(
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel,
        ShippingMethodInterface $shippingMethod
    ) {
        $shipment->getOrder()->willReturn($order);
        $shipment->getMethod()->willReturn($shippingMethod);

        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');
        $channel->getName()->willReturn('WEB');

        $shippingMethod->getName()->willReturn('UPS');

        $this
            ->shouldThrow(ChannelNotDefinedException::class)
            ->during('calculate', [$shipment, ['amount' => 200]])
        ;
    }
}
