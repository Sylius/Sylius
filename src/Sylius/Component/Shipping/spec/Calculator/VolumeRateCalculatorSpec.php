<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Shipping\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Shipping\Calculator\CalculatorInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;

final class VolumeRateCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Calculator\VolumeRateCalculator');
    }

    function it_should_implement_Sylius_shipping_calculator_interface()
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_returns_volume_rate_type()
    {
        $this->getType()->shouldReturn('volume_rate');
    }

    function it_should_calculate_the_flat_rate_amount_configured_on_the_method(ShipmentInterface $subject)
    {
        $subject->getShippingVolume()->willReturn(100);

        $this->calculate($subject, ['amount' => 500, 'division' => 2])->shouldReturn(500 * 100 / 2);
    }

    function its_calculated_value_should_be_an_integer(ShipmentInterface $subject)
    {
        $subject->getShippingVolume()->willReturn(100);

        $this->calculate($subject, ['amount' => 500, 'division' => 2])->shouldBeInteger();
    }
}
