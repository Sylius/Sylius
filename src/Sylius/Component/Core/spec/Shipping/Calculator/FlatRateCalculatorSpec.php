<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Shipping\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Exception\MissingChannelConfigurationException;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;

final class FlatRateCalculatorSpec extends ObjectBehavior
{
    function it_implements_shipping_calculator_interface(): void
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_returns_flat_rate_type(CalculatorInterface $calculator): void
    {
        $calculator->getType()->willReturn('flat_rate');

        $this->getType()->shouldReturn('flat_rate');
    }

    function it_calculates_the_flat_rate_amount_configured_on_the_method(
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel,
    ): void {
        $shipment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');

        $this->calculate($shipment, ['WEB' => ['amount' => 1500]])->shouldReturn(1500);
    }

    function it_throws_a_channel_not_defined_exception_if_channel_code_key_does_not_exist(
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel,
        ShippingMethodInterface $shippingMethod,
    ): void {
        $shipment->getOrder()->willReturn($order);
        $shipment->getMethod()->willReturn($shippingMethod);

        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');
        $channel->getName()->willReturn('WEB');

        $shippingMethod->getName()->willReturn('UPS');

        $this
            ->shouldThrow(MissingChannelConfigurationException::class)
            ->during('calculate', [$shipment, ['amount' => 1500]])
        ;
    }
}
