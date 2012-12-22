<?php

namespace spec\Sylius\Bundle\TaxationBundle\Calculator;

use PHPSpec2\ObjectBehavior;

/**
 * Delegating tax calculator spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DelegatingTaxCalculator extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\TaxationBundle\Calculator\DelegatingTaxCalculator');
    }

    function it_should_be_a_Sylius_tax_calculator()
    {
        $this->shouldImplement('Sylius\Bundle\TaxationBundle\Calculator\TaxCalculatorInterface');
    }

    function it_should_initialize_calculators_array_by_default()
    {
        $this->getCalculators()->shouldReturn(array());
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Calculator\TaxCalculatorInterface $calculator
     */
    function it_should_register_calculator_properly($calculator)
    {
        $this->hasCalculator('default')->shouldReturn(false);
        $this->registerCalculator('default', $calculator);
        $this->hasCalculator('default')->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Calculator\TaxCalculatorInterface $calculator
     */
    function it_should_unregister_calculator_properly($calculator)
    {
        $this->registerCalculator('default', $calculator);
        $this->hasCalculator('default')->shouldReturn(true);

        $this->unregisterCalculator('default');
        $this->hasCalculator('default')->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Calculator\TaxCalculatorInterface $calculator
     */
    function it_should_retrieve_registered_calculator_by_name($calculator)
    {
        $this->registerCalculator('default', $calculator);
        $this->getCalculator('default')->shouldReturn($calculator);
    }

    function it_should_complain_if_trying_to_retrieve_non_existing_calculator()
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringGetCalculator('default')
        ;
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxRateInterface            $rate
     * @param Sylius\Bundle\TaxationBundle\Calculator\TaxCalculatorInterface $calculator
     */
    function it_should_delegate_calculation_to_a_correct_calculator($rate, $calculator)
    {
        $this->registerCalculator('default', $calculator);
        $rate->getCalculator()->willReturn('default');

        $calculator->calculate(100, $rate)->shouldBeCalled()->willReturn(23);

        $this->calculate(100, $rate)->shouldReturn(23);
    }

    /**
     * @param Sylius\Bundle\TaxationBundle\Model\TaxRateInterface            $rate
     * @param Sylius\Bundle\TaxationBundle\Calculator\TaxCalculatorInterface $calculator
     */
    function it_should_complain_if_correct_calculator_doesnt_exist_for_given_rate($rate, $calculator)
    {
        $this->registerCalculator('default', $calculator);
        $rate->getCalculator()->willReturn('custom');

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringCalculate(100, $rate)
        ;
    }
}
