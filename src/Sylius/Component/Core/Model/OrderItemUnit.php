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
     * @var string
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
     * @var OrderItemInterface
     */
    protected $orderItem;

    /**
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * @var \DateTime
     */
    protected $updatedAt;

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
    public function setStockable(StockableInterface $stockable)
    {
        $this->orderItem->setVariant($stockable);
    }

    /**
     * {@inheritdoc}
     */
    public function getSku()
    {
        return $this->orderItem->getVariant()->getSku();
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
    public function setShippable(ShippableInterface $shippable)
    {
        $this->orderItem->setVariant($shippable);
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
    public function getOrderItem()
    {
        return $this->orderItem;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrderItem(OrderItemInterface $orderItem = null)
    {
        $this->orderItem = $orderItem;
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
