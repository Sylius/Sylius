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
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class WeightRateCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Calculator\WeightRateCalculator');
    }

    function it_should_implement_Sylius_shipping_calculator_interface()
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_returns_weight_rate_type()
    {
        $this->getType()->shouldReturn('weight_rate');
    }

    function it_should_calculate_the_flat_rate_amount_configured_on_the_method(ShippingSubjectInterface $subject)
    {
        $subject->getShippingWeight()->willReturn(10);

        $this->calculate($subject, ['fixed' => 200, 'variable' => 500, 'division' => 1])->shouldReturn(200 + 500 * 10);
    }

    function its_calculated_value_should_be_an_integer(ShippingSubjectInterface $subject)
    {
        $subject->getShippingWeight()->willReturn(10);

        $this->calculate($subject, ['fixed' => 200, 'variable' => 500, 'division' => 1])->shouldBeInteger();
    }
}
