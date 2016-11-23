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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Shipping\Calculator\PerUnitRateCalculator;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class PerUnitRateCalculatorSpec extends ObjectBehavior
{
    function let(CalculatorInterface $calculator, ChannelContextInterface $channelContext)
    {
        $this->beConstructedWith($calculator, $channelContext);
    }

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
        ChannelContextInterface $channelContext,
        ShipmentInterface $shipment,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');
        $shipment->getShippingUnitCount()->willReturn(10);

        $this->calculate($shipment, ['WEB' => ['amount' => 200]])->shouldReturn(2000);
    }

    function its_calculated_value_is_an_integer(
        ChannelContextInterface $channelContext,
        ShipmentInterface $shipment,
        ChannelInterface $channel
    ) {
        $channelContext->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');
        $shipment->getShippingUnitCount()->willReturn(10);

        $this->calculate($shipment, ['WEB' => ['amount' => 700]])->shouldBeInteger();
    }
}
