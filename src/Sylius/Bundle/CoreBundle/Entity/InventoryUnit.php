<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Entity;

use Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\InventoryBundle\Entity\InventoryUnit as BaseInventoryUnit;
use Sylius\Bundle\ShippingBundle\Model\ShipmentInterface;
use Sylius\Bundle\ShippingBundle\Model\ShipmentItemInterface;
use Sylius\Bundle\ShippingBundle\Model\ShippableInterface;

/**
 * Custom inventory unit class.
 * Can be attached to order.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryUnit extends BaseInventoryUnit implements InventoryUnitInterface
{
    /**
     * Id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Order.
     *
     * @var OrderInterface
     */
    protected $order;

    /**
     * Shipment
     *
     * @var ShipmentInterface
     */
    protected $shipment;

    /**
     * Shipping state.
     *
     * @var string ShipmentItemInterface::STATE_*
     */
    protected $shippingState;

    public function __construct()
    {
        parent::__construct();

        $this->shippingState = ShipmentItemInterface::STATE_READY;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getOrder()
    {
        return $this->order;
    }

    public function setOrder(OrderInterface $order = null)
    {
        $this->order = $order;
    }

    /**
     * {@inheritdoc}
     */
    public function getShipment()
    {
        return $this->shipment;
    }

    /**
     * {@inheritdoc}
     */
    public function setShipment(ShipmentInterface $shipment = null)
    {
        $this->shipment = $shipment;
    }

    public function getShippable()
    {
        return $this->getStockable();
    }

    public function setShippable(ShippableInterface $shippable)
    {
        $this->setStockable($shippable);
    }

    public function getShippingState()
    {
        return $this->shippingState;
    }

    public function setShippingState($state)
    {
        $this->shippingState = $state;
    }
}
