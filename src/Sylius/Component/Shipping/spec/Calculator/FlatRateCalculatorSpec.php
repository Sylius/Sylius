<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Shipping\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class FlatRateCalculatorSpec extends ObjectBehavior
{
    function it_should_implement_shipping_calculator_interface(): void
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_returns_flat_rate_type(): void
    {
        $this->getType()->shouldReturn('flat_rate');
    }

    function it_should_calculate_the_flat_rate_amount_configured_on_the_method(ShipmentInterface $shipment): void
    {
        $this->calculate($shipment, ['amount' => 1500])->shouldReturn(1500);
    }

    function its_calculated_value_should_be_an_integer(ShipmentInterface $shipment): void
    {
        $this->calculate($shipment, ['amount' => 410])->shouldBeInteger();
    }
}
