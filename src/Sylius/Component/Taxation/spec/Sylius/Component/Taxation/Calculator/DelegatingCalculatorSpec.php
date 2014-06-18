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
 */
class DelegatingCalculatorSpec extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Taxation\Calculator\DelegatingCalculator');
    }

    function it_should_be_a_Sylius_tax_calculator()
    {
        $this->shouldImplement('Sylius\Component\Taxation\Calculator\CalculatorInterface');
    }

    function it_should_initialize_calculators_array_by_default()
    {
        $this->getCalculators()->shouldReturn(array());
    }

    function it_should_register_calculator_properly(CalculatorInterface $calculator)
    {
        $this->hasCalculator('default')->shouldReturn(false);
        $this->registerCalculator('default', $calculator);
        $this->hasCalculator('default')->shouldReturn(true);
    }

    function it_should_unregister_calculator_properly(CalculatorInterface $calculator)
    {
        $this->registerCalculator('default', $calculator);
        $this->hasCalculator('default')->shouldReturn(true);

        $this->unregisterCalculator('default');
        $this->hasCalculator('default')->shouldReturn(false);
    }

    function it_should_retrieve_registered_calculator_by_name(CalculatorInterface $calculator)
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

    function it_should_delegate_calculation_to_a_correct_calculator(
        TaxRateInterface $rate,
        CalculatorInterface $calculator
    )
    {
        $this->registerCalculator('default', $calculator);
        $rate->getCalculator()->willReturn('default');

        $calculator->calculate(100, $rate)->shouldBeCalled()->willReturn(23);

        $this->calculate(100, $rate)->shouldReturn(23);
    }

    function it_should_complain_if_correct_calculator_doesnt_exist_for_given_rate(
        TaxRateInterface $rate,
        CalculatorInterface $calculator
    )
    {
        $this->registerCalculator('default', $calculator);
        $rate->getCalculator()->willReturn('custom');

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringCalculate(100, $rate)
        ;
    }
}
