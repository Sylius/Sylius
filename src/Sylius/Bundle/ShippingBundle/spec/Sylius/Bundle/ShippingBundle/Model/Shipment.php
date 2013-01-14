<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ShippingBundle\Model;

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Model\ShipmentInterface;

/**
 * Shipment model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Shipment extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Model\Shipment');
    }

    function it_should_implement_Sylius_shipment_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShipmentInterface');
    }

    function it_should_not_have_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_should_have_ready_state_by_default()
    {
        $this->getState()->shouldReturn(ShipmentInterface::STATE_READY);
    }

    function its_state_should_be_mutable()
    {
        $this->setState(ShipmentInterface::STATE_PENDING);
        $this->getState()->shouldReturn(ShipmentInterface::STATE_PENDING);
    }

    function it_should_not_have_shipping_method_by_default()
    {
        $this->getMethod()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $shippingMethod
     */
    function its_shipping_method_should_be_mutable($shippingMethod)
    {
        $this->setMethod($shippingMethod);
        $this->getMethod()->shouldReturn($shippingMethod);
    }

    function it_should_initialize_items_collection_by_default()
    {
        $this->getItems()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface $shipmentItem
     */
    function it_should_add_items_properly($shipmentItem)
    {
        $this->hasItem($shipmentItem)->shouldReturn(false);

        $shipmentItem->setShipment($this)->shouldBeCalled();
        $this->addItem($shipmentItem);

        $this->hasItem($shipmentItem)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface $shipmentItem
     */
    function it_should_remove_items_properly($shipmentItem)
    {
        $this->hasItem($shipmentItem)->shouldReturn(false);

        $shipmentItem->setShipment($this)->shouldBeCalled();
        $this->addItem($shipmentItem);

        $shipmentItem->setShipment(null)->shouldBeCalled();
        $this->removeItem($shipmentItem);

        $this->hasItem($shipmentItem)->shouldReturn(false);
    }

    function it_should_not_have_tracking_code_by_default()
    {
        $this->getTracking()->shouldReturn(null);
    }

    function its_tracking_code_should_be_mutable()
    {
        $this->setTracking('5346172074');
        $this->getTracking()->shouldReturn('5346172074');
    }

    function it_should_not_be_tracked_by_default()
    {
        $this->shouldNotBeTracked();
    }

    function it_should_be_tracked_only_if_tracking_code_is_defined()
    {
        $this->shouldNotBeTracked();
        $this->setTracking('5346172074');
        $this->shouldBeTracked();
        $this->setTracking(null);
        $this->shouldNotBeTracked();
    }

    function it_should_initialize_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function it_should_not_have_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }
}
