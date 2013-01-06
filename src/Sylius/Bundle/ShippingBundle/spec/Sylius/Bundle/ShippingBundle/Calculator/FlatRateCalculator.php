<?php

namespace spec\Sylius\Bundle\ShippingBundle\Calculator;

use PHPSpec2\ObjectBehavior;

/**
 * Flat shipment rate calculator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FlatRateCalculator extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\FlatRateCalculator');
    }

    function it_should_be_a_Sylius_shipping_charge_calculator()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Calculator\ShippingChargeCalculatorInterface');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface       $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $method
     */
    function it_should_calculate_the_flat_amount_configured_in_a_method($shipment, $method)
    {
        $shipment->getMethod()->willReturn($method);
        $method->getConfiguration()->willReturn(array('amount' => 15.00));

        $this->calculate($shipment)->shouldReturn(15.00);
    }
}
