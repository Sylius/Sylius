<?php

namespace spec\Sylius\Bundle\ShippingBundle\Calculator;

use Doctrine\Common\Collections\ArrayCollection;
use PHPSpec2\ObjectBehavior;

/**
 * Flat rate per item calculator spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class PerItemRateCalculator extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\PerItemRateCalculator');
    }

    function it_should_be_a_Sylius_shipping_charge_calculator()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Calculator\ShippingChargeCalculatorInterface');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface       $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $method
     */
    function it_should_calculate_the_per_item_amount_configured_in_a_method($shipment, $method)
    {
        $shipment->getItems()->willReturn(new ArrayCollection(array('1', '2', '3', '4 items')));
        $shipment->getMethod()->willReturn($method);

        $method->getConfiguration()->willReturn(array('amount' => 10.00));

        $this->calculate($shipment)->shouldReturn(40.00);
    }
}

