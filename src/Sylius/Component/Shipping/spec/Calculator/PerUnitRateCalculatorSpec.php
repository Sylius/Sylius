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
use Sylius\Component\Shipping\Calculator\PerUnitRateCalculator;
use Sylius\Component\Shipping\Model\ShipmentInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class PerUnitRateCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(PerUnitRateCalculator::class);
    }

    function it_should_implement_Sylius_shipping_calculator_interface()
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_returns_per_unit_type()
    {
        $this->getType()->shouldReturn('per_unit_rate');
    }

    function it_should_calculate_the_total_with_the_per_unit_amount_configured_on_the_method(
        ShipmentInterface $subject
    ) {
        $subject->getShippingUnitCount()->willReturn(11);

        $this->calculate($subject, ['amount' => 200])->shouldReturn(2200);
    }

    function its_calculated_value_should_be_an_integer(ShipmentInterface $subject)
    {
        $subject->getShippingUnitCount()->willReturn(6);

        $this->calculate($subject, ['amount' => 200])->shouldBeInteger();
    }
}
