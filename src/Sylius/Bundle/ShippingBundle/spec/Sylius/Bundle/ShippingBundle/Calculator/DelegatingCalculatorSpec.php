<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Model\ShipmentInterface;
use Sylius\Bundle\CoreBundle\Model\ShippingMethodInterface;
use Sylius\Bundle\ShippingBundle\Calculator\CalculatorInterface;
use Sylius\Bundle\ShippingBundle\Calculator\Registry\CalculatorRegistryInterface;

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
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\DelegatingCalculator');
    }

    function it_implements_Sylius_delegating_shipping_calculator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Calculator\DelegatingCalculatorInterface');
    }

    function it_should_complain_if_shipment_has_no_method_defined(ShipmentInterface $shipment)
    {
        $shipment->getMethod()->willReturn(null);

        $this
            ->shouldThrow('Sylius\Bundle\ShippingBundle\Calculator\UndefinedShippingMethodException')
            ->duringCalculate($shipment)
        ;
    }

    function it_should_delegate_calculation_to_a_calculator_defined_on_shipping_method(
        $registry,
        ShipmentInterface $shipment,
        ShippingMethodInterface $method,
        CalculatorInterface $calculator
    )
    {
        $shipment->getMethod()->willReturn($method);

        $method->getCalculator()->willReturn('default');
        $method->getConfiguration()->willReturn(array());

        $registry->getCalculator('default')->willReturn($calculator);
        $calculator->calculate($shipment, array())->shouldBeCalled()->willReturn(1000);

        $this->calculate($shipment, array())->shouldReturn(1000);
    }
}
