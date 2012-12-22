<?php

namespace spec\Sylius\Bundle\TaxationBundle\Calculator;

use PHPSpec2\ObjectBehavior;

/**
 * Default calculator spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DefaultTaxCalculator extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Calculator\DefaultTaxCalculator');
    }

    function it_should_be_a_Sylius_tax_calculator()
    {
        $this->shouldImplement('Sylius\Bundle\TaxationBundle\Calculator\TaxCalculatorInterface');
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxRateInterface $rate
     */
    function it_should_calculate_tax_from_base_and_tax_rate($rate)
    {
        $rate->getAmount()->willReturn(0.23);

        $this->calculate(100, $rate)->shouldReturn(23.00);
        $this->calculate(1000, $rate)->shouldReturn(230.00);
        $this->calculate(2495.99, $rate)->shouldReturn(574.07);
    }
}
