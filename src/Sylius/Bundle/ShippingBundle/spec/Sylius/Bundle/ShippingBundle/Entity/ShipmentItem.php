<?php

namespace spec\Sylius\Bundle\ShippingBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Shipment item mapped superclass spec.
 *
 * @author Pawęł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShipmentItem extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Entity\ShipmentItem');
    }

    function it_should_be_a_Sylius_shipment_item()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface');
    }

    function it_should_extend_Sylius_shipment_item_model()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Model\ShipmentItem');
    }
}
