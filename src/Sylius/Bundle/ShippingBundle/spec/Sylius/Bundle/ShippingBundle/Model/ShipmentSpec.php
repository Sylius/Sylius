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

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ShippingBundle\Model\ShipmentInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class ShipmentSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\ShippingBundle\Model\Shipment');
    }

    function it_implements_Sylius_shipment_interface()
    {
        $this->shouldImplement('Sylius\Bundle\ShippingBundle\Model\ShipmentInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_ready_state_by_default()
    {
        $this->getState()->shouldReturn(ShipmentInterface::STATE_CHECKOUT);
    }

    function its_state_is_mutable()
    {
        $this->setState(ShipmentInterface::STATE_PENDING);
        $this->getState()->shouldReturn(ShipmentInterface::STATE_PENDING);
    }

    function it_has_no_shipping_method_by_default()
    {
        $this->getMethod()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShippingMethodInterface $shippingMethod
     */
    function its_shipping_method_is_mutable($shippingMethod)
    {
        $this->setMethod($shippingMethod);
        $this->getMethod()->shouldReturn($shippingMethod);
    }

    function it_initializes_items_collection_by_default()
    {
        $this->getItems()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface $shipmentItem
     */
    function it_adds_items($shipmentItem)
    {
        $this->hasItem($shipmentItem)->shouldReturn(false);

        $shipmentItem->setShipment($this)->shouldBeCalled();
        $this->addItem($shipmentItem);

        $this->hasItem($shipmentItem)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface $shipmentItem
     */
    function it_removes_item($shipmentItem)
    {
        $this->hasItem($shipmentItem)->shouldReturn(false);

        $shipmentItem->setShipment($this)->shouldBeCalled();
        $this->addItem($shipmentItem);

        $shipmentItem->setShipment(null)->shouldBeCalled();
        $this->removeItem($shipmentItem);

        $this->hasItem($shipmentItem)->shouldReturn(false);
    }

    function it_has_no_tracking_code_by_default()
    {
        $this->getTracking()->shouldReturn(null);
    }

    function its_tracking_code_is_mutable()
    {
        $this->setTracking('5346172074');
        $this->getTracking()->shouldReturn('5346172074');
    }

    function it_is_not_tracked_by_default()
    {
        $this->shouldNotBeTracked();
    }

    function it_is_tracked_only_if_tracking_code_is_defined()
    {
        $this->shouldNotBeTracked();
        $this->setTracking('5346172074');
        $this->shouldBeTracked();
        $this->setTracking(null);
        $this->shouldNotBeTracked();
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    function its_creation_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
