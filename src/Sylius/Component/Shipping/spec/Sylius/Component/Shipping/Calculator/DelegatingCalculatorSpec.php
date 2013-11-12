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
use Sylius\Component\Shipping\Calculator\Registry\CalculatorRegistryInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class DelegatingCalculatorSpec extends ObjectBehavior
{

    function let(CalculatorRegistryInterface $registry)
    {
        $this->beConstructedWith($registry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Shipping\Calculator\DelegatingCalculator');
    }

    function it_implements_Sylius_delegating_shipping_calculator_interface()
    {
        $this->shouldImplement('Sylius\Component\Shipping\Calculator\DelegatingCalculatorInterface');
    }

    function it_should_complain_if_shipment_has_no_method_defined(ShipmentInterface $shipment)
    {
        $shipment->getMethod()->willReturn(null);

        $this
            ->shouldThrow('Sylius\Component\Shipping\Calculator\UndefinedShippingMethodException')
            ->duringCalculate($shipment)
        ;
    }

    function it_should_delegate_calculation_to_a_calculator_defined_on_shipping_method($registry, ShipmentInterface $shipment, ShippingMethodInterface $method, CalculatorInterface $calculator)
    {
        $shipment->getMethod()->willReturn($method);

        $method->getCalculator()->willReturn('default');
        $method->getConfiguration()->willReturn(array());

        $registry->getCalculator('default')->willReturn($calculator);
        $calculator->calculate($shipment, array())->shouldBeCalled()->willReturn(1000);

        $this->calculate($shipment, array())->shouldReturn(1000);
    }
}
