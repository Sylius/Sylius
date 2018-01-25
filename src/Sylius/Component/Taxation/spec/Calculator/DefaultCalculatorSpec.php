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

namespace spec\Sylius\Component\Taxation\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Taxation\Calculator\CalculatorInterface;
use Sylius\Component\Taxation\Model\TaxRateInterface;

final class DefaultCalculatorSpec extends ObjectBehavior
{
    function it_implements_Sylius_tax_calculator_interface(): void
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_calculates_tax_as_percentage_of_given_base_if_rate_is_not_included_in_price(
        TaxRateInterface $rate
    ): void {
        $rate->isIncludedInPrice()->willReturn(false);
        $rate->getAmount()->willReturn(0.23);

        $this->calculate(10000, $rate)->shouldReturn(2300.00);
        $this->calculate(100000, $rate)->shouldReturn(23000.00);
        $this->calculate(249599, $rate)->shouldReturn(57408.00);
    }

    function it_calculates_correct_tax_for_given_base_if_rate_is_included_in_price(
        TaxRateInterface $rate
    ): void {
        $rate->isIncludedInPrice()->willReturn(true);
        $rate->getAmount()->willReturn(0.23);

        $this->calculate(10000, $rate)->shouldReturn(1870.00);

        $rate->getAmount()->willReturn(0.2);
        $this->calculate(315, $rate)->shouldReturn(53.00);
    }
}
