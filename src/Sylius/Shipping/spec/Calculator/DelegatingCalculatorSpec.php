<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Shipping\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Registry\ServiceRegistryInterface;
use Sylius\Shipping\Calculator\CalculatorInterface;
use Sylius\Shipping\Calculator\DelegatingCalculatorInterface;
use Sylius\Shipping\Calculator\UndefinedShippingMethodException;
use Sylius\Shipping\Model\ShipmentInterface;
use Sylius\Shipping\Model\ShippingMethodInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class DelegatingCalculatorSpec extends ObjectBehavior
{
    function let(ServiceRegistryInterface $registry)
    {
        $this->beConstructedWith($registry);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Shipping\Calculator\DelegatingCalculator');
    }

    function it_implements_Sylius_delegating_shipping_calculator_interface()
    {
        $this->shouldImplement(DelegatingCalculatorInterface::class);
    }

    function it_should_complain_if_shipment_has_no_method_defined(ShipmentInterface $shipment)
    {
        $shipment->getMethod()->willReturn(null);

        $this
            ->shouldThrow(UndefinedShippingMethodException::class)
            ->duringCalculate($shipment)
        ;
    }

    function it_should_delegate_calculation_to_a_calculator_defined_on_shipping_method(
        $registry,
        ShipmentInterface $shipment,
        ShippingMethodInterface $method,
        CalculatorInterface $calculator
    ) {
        $shipment->getMethod()->willReturn($method);

        $method->getCalculator()->willReturn('default');
        $method->getConfiguration()->willReturn([]);

        $registry->get('default')->willReturn($calculator);
        $calculator->calculate($shipment, [])->shouldBeCalled()->willReturn(1000);

        $this->calculate($shipment, [])->shouldReturn(1000);
    }
}
