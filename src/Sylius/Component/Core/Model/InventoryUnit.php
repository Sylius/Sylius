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

use Doctrine\Common\Collections\ArrayCollection;
use Sylius\Component\Inventory\Model\InventoryUnit as BaseInventoryUnit;
use Sylius\Component\Order\Model\AdjustmentInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;
use Sylius\Component\Shipping\Model\ShippableInterface;


/**
 * Custom inventory unit class.
 * Can be attached to order.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryUnit extends BaseInventoryUnit implements InventoryUnitInterface
{
    /**
     * Order item.
     *
     * @var OrderItemInterface
     */
    protected $orderItem;

    /**
     * Shipment
     *
     * @var BaseShipmentInterface
     */
    protected $shipment;

    /**
     * Shipping state.
     *
     * @var string ShipmentInterface::STATE_*
     */
    protected $shippingState = ShipmentInterface::STATE_CHECKOUT;

    /**
     * @var AdjustmentInterface[]
     */
    protected $adjustments;

    /**
     * @var integer
     */
    protected $adjustmentsTotal;

    public function __construct()
    {
        parent::__construct();

        $this->adjustments = new ArrayCollection();
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

    /**
     * {@inheritDoc}
     */
    public function getAdjustments($type = null)
    {
        if (null == $type) {
            return $this->adjustments;
        }

        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) use ($type) {
            return $type === $adjustment->getType();
        });
    }

    /**
     * {@inheritDoc}
     */
    public function addAdjustment(AdjustmentInterface $adjustment)
    {
        if ($this->adjustments->contains($adjustment)) {
            return;
        }

        $adjustment->setAdjustable($this);
        $this->adjustments->add($adjustment);
    }

    /**
     * {@inheritDoc}
     */
    public function removeAdjustment(AdjustmentInterface $adjustment)
    {
        if (!$this->adjustments->contains($adjustment)) {
            return;
        }

        $this->adjustments->removeElement($adjustment);
    }

    /**
     * {@inheritDoc}
     */
    public function getAdjustmentsTotal($type = null)
    {
        $amount = 0;

        foreach ($this->adjustments as $adjustment) {
            if ($type && $type !== $adjustment->getType()) {
                continue;
            }

            $amount += $adjustment->getAmount();
        }

        $this->adjustmentsTotal = $amount;

        return $this->adjustmentsTotal;
    }

    /**
     * {@inheritDoc}
     */
    public function removeAdjustments($type)
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            if ($type === $adjustment->getType() && !$adjustment->isLocked()) {
                $this->removeAdjustment($adjustment);
            }
        }
    }

    public function clearAdjustments()
    {
        $this->adjustments->clear();
    }
}
