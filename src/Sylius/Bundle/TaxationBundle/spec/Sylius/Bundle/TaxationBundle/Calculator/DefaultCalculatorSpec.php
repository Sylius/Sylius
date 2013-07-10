<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\TaxationBundle\Calculator;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DefaultCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Calculator\DefaultCalculator');
    }

    function it_implements_Sylius_tax_calculator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\TaxationBundle\Calculator\CalculatorInterface');
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxRateInterface $rate
     */
    function it_calculates_tax_as_percentage_of_given_base_if_rate_is_not_included_in_price($rate)
    {
        $rate->isIncludedInPrice()->willReturn(false);
        $rate->getAmount()->willReturn(0.23);

        $this->calculate(10000, $rate)->shouldReturn(2300);
        $this->calculate(100000, $rate)->shouldReturn(23000);
        $this->calculate(249599, $rate)->shouldReturn(57408);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxRateInterface $rate
     */
    function it_calculates_correct_tax_for_given_base_if_rate_is_included_in_price($rate)
    {
        $rate->isIncludedInPrice()->willReturn(true);
        $rate->getAmount()->willReturn(0.23);

        $this->calculate(10000, $rate)->shouldReturn(1870);
    }
}
