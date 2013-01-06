<?php

namespace spec\Sylius\Bundle\ShippingBundle\Calculator;

use Doctrine\Common\Collections\ArrayCollection;
use PHPSpec2\ObjectBehavior;

/**
 * Flexible rate calculator spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class FlexibleRateCalculator extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Calculator\FlexibleRateCalculator');
    }

    function it_should_be_a_Sylius_shipping_charge_calculator()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Calculator\ShippingChargeCalculatorInterface');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface       $shipment
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $method
     */
    function it_should_calculate_the_flat_amount_for_first_item_and_other_for_additional_items($shipment, $method)
    {
        $shipment->getItems()->willReturn(new ArrayCollection(array('1', '2', '3', '4 items')));
        $shipment->getMethod()->willReturn($method);

        $method->getConfiguration()->willReturn(array('amount' => 10.00, 'rate' => 5.00, 'limit' => 0));

        $this->calculate($shipment)->shouldReturn(25.00);
    }
}

