<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Entity;

use PHPSpec2\ObjectBehavior;

/**
 * Order model spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Order extends ObjectBehavior
{
    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Entity\Order');
    }

    function it_should_implement_Sylius_order_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Model\OrderInterface');
    }

    function it_should_extend_Sylius_order_mapped_superclass()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Entity\Order');
    }

    function it_should_not_have_user_defined_by_default()
    {
        $this->getUser()->shouldReturn(null);
    }

    /**
     * @param FOS\UserBundle\Model\UserInterface $user
     */
    function it_should_allow_defining_user($user)
    {
        $this->setUser($user);
        $this->getUser()->shouldReturn($user);
    }

    function it_should_not_have_shipping_address_by_default()
    {
        $this->getShippingAddress()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function it_should_allow_defining_shipping_address($address)
    {
        $this->setShippingAddress($address);
        $this->getShippingAddress()->shouldReturn($address);
    }

    function it_should_not_have_billing_address_by_default()
    {
        $this->getBillingAddress()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\AddressingBundle\Model\AddressInterface $address
     */
    function it_should_allow_defining_billing_address($address)
    {
        $this->setBillingAddress($address);
        $this->getBillingAddress()->shouldReturn($address);
    }

    function it_should_initialize_inventory_units_collection_by_default()
    {
        $this->getInventoryUnits()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface $unit
     */
    function it_should_add_inventory_units_properly($unit)
    {
        $unit->setOrder($this)->shouldBeCalled();
        $this->addInventoryUnit($unit);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface $unit
     */
    function it_should_remove_inventory_units_properly($unit)
    {
        $unit->setOrder($this)->shouldBeCalled();
        $this->addInventoryUnit($unit);

        $unit->setOrder(null)->shouldBeCalled();
        $this->removeInventoryUnit($unit);
    }

    function it_should_initialize_shipments_collection_by_default()
    {
        $this->getShipments()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface $shipment
     */
    function it_should_add_shipment_properly($shipment)
    {
        $this->hasShipment($shipment)->shouldReturn(false);

        $shipment->setOrder($this)->shouldBeCalled();
        $this->addShipment($shipment);

        $this->hasShipment($shipment)->shouldReturn(true);
    }

    /**
     * @param Sylius\Bundle\ShippingBundle\Model\ShipmentInterface $shipment
     */
    function it_should_remove_shipment_properly($shipment)
    {
        $shipment->setOrder($this)->shouldBeCalled();
        $this->addShipment($shipment);

        $this->hasShipment($shipment)->shouldReturn(true);

        $shipment->setOrder(null)->shouldBeCalled();
        $this->removeShipment($shipment);

        $this->hasShipment($shipment)->shouldReturn(false);
    }
}
