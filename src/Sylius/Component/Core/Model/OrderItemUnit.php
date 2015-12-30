<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Order\Model\OrderItemUnit as BaseOrderItemUnit;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemUnit extends BaseOrderItemUnit implements OrderItemUnitInterface
{
    /**
     * @var string InventoryUnitInterface::STATE_*
     */
    protected $inventoryState = InventoryUnitInterface::STATE_CHECKOUT;

    /**
     * @var BaseShipmentInterface
     */
    protected $shipment;

    /**
     * @var string BaseShipmentInterface::STATE_*
     */
    protected $shippingState = BaseShipmentInterface::STATE_CHECKOUT;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * @param OrderItemInterface $orderItem
     */
    public function __construct(OrderItemInterface $orderItem)
    {
        parent::__construct($orderItem);

        $this->createdAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getStockable()
    {
        return $this->orderItem->getVariant();
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryName()
    {
        return $this->orderItem->getVariant()->getInventoryName();
    }

    /**
     * {@inheritdoc}
     */
    public function getInventoryState()
    {
        return $this->inventoryState;
    }

    /**
     * {@inheritdoc}
     */
    public function setInventoryState($state)
    {
        $this->inventoryState = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function isSold()
    {
        return InventoryUnitInterface::STATE_SOLD === $this->inventoryState;
    }

    /**
     * {@inheritdoc}
     */
    public function isBackordered()
    {
        return InventoryUnitInterface::STATE_BACKORDERED === $this->inventoryState;
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
    }

    /**
     * {@inheritdoc}
     */
    public function getShippable()
    {
        return $this->orderItem->getVariant();
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
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }
}
