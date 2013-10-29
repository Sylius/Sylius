<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CoreBundle\Model;

use Sylius\Bundle\InventoryBundle\Model\InventoryUnit as BaseInventoryUnit;
use Sylius\Bundle\ShippingBundle\Model\ShipmentInterface as BaseShipmentInterface;
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
     * @var BaseShipmentInterface
     */
    protected $shipment;

    /**
     * Shipping state.
     *
     * @var string ShipmentItemInterface::STATE_*
     */
    protected $shippingState;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Last update time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->shippingState = ShipmentItemInterface::STATE_READY;
    }

    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(OrderInterface $order = null)
    {
        $this->order = $order;

        return $this;
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
    public function setShipment(BaseShipmentInterface $shipment = null)
    {
        $this->shipment = $shipment;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippable()
    {
        return $this->getStockable();
    }

    /**
     * {@inheritdoc}
     */
    public function setShippable(ShippableInterface $shippable)
    {
        $this->setStockable($shippable);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getShippingState()
    {
        return $this->shippingState;
    }

    /**
     * {@inheritdoc}
     */
    public function setShippingState($state)
    {
        $this->shippingState = $state;

        return $this;
    }
}
