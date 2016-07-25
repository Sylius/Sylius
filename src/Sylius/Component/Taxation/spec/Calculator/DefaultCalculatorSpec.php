<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Taxation\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
final class DefaultCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxation\Calculator\DefaultCalculator');
    }

    function it_implements_Sylius_tax_calculator_interface()
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_calculates_tax_as_percentage_of_given_base_if_rate_is_not_included_in_price(
        TaxRateInterface $rate
    ) {
        $rate->isIncludedInPrice()->willReturn(false);
        $rate->getAmount()->willReturn(0.23);

        $this->calculate(10000, $rate)->shouldReturn(2300);
        $this->calculate(100000, $rate)->shouldReturn(23000);
        $this->calculate(249599, $rate)->shouldReturn(57408);
    }

    function it_calculates_correct_tax_for_given_base_if_rate_is_included_in_price(
        TaxRateInterface $rate
    ) {
        $rate->isIncludedInPrice()->willReturn(true);
        $rate->getAmount()->willReturn(0.23);

        $this->calculate(10000, $rate)->shouldReturn(1870);

        $rate->getAmount()->willReturn(0.2);
        $this->calculate(315, $rate)->shouldReturn(53);
    }
}
