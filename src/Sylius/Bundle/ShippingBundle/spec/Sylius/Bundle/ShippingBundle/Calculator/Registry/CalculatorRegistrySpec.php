<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Calculator\Registry;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CalculatorRegistrySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorRegistry');
    }

    function it_implements_Sylius_shipping_calculator_registry()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorRegistryInterface');
    }

    function it_initializes_calculators_array_by_default()
    {
        $this->getCalculators()->shouldReturn(array());
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Calculator\CalculatorInterface $calculator
     */
    function it_registers_calculator_under_given_name($calculator)
    {
        $this->hasCalculator('default')->shouldReturn(false);
        $this->registerCalculator('default', $calculator);
        $this->hasCalculator('default')->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Calculator\CalculatorInterface $calculator
     */
    function it_throws_exception_if_trying_to_register_calculator_with_taken_name($calculator)
    {
        $this->registerCalculator('default', $calculator);

        $this
            ->shouldThrow('Sylius\Bundle\ShippingBundle\Calculator\Registry\ExistingCalculatorException')
            ->duringRegisterCalculator('default', $calculator)
        ;
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Calculator\CalculatorInterface $calculator
     */
    function it_unregisters_calculator_with_given_name($calculator)
    {
        $this->registerCalculator('default', $calculator);
        $this->hasCalculator('default')->shouldReturn(true);

        $this->unregisterCalculator('default');
        $this->hasCalculator('default')->shouldReturn(false);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Calculator\CalculatorInterface $calculator
     */
    function it_provides_registered_calculator_by_name($calculator)
    {
        $this->registerCalculator('default', $calculator);
        $this->getCalculator('default')->shouldReturn($calculator);
    }

    function it_throws_exception_if_trying_to_retrieve_non_existing_calculator()
    {
        $this
            ->shouldThrow('Sylius\Bundle\ShippingBundle\Calculator\Registry\NonExistingCalculatorException')
            ->duringGetCalculator('default')
        ;
    }
}
