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
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Shipping\Calculator\FlatRateCalculator;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class FlatRateCalculatorSpec extends ObjectBehavior
{
    function let(CalculatorInterface $calculator)
    {
        $this->beConstructedWith($calculator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(FlatRateCalculator::class);
    }

    function it_implements_shipping_calculator_interface()
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_returns_flat_rate_type(CalculatorInterface $calculator)
    {
        $calculator->getType()->willReturn('flat_rate');

        $this->getType()->shouldReturn('flat_rate');
    }

    function it_calculates_the_flat_rate_amount_configured_on_the_method(
        CalculatorInterface $calculator,
        ShipmentInterface $shipment,
        OrderInterface $order,
        ChannelInterface $channel
    ) {
        $shipment->getOrder()->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $channel->getCode()->willReturn('WEB');

        $calculator->calculate($shipment, ['amount' => 1500])->shouldBeCalled();

        $this->calculate($shipment, ['WEB' => ['amount' => 1500]]);
    }
}
