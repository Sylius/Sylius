<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class OrderItem implements OrderItemInterface
{
    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var OrderInterface
     */
    protected $order;

    /**
     * @var int
     */
    protected $quantity = 1;

    /**
     * @var int
     */
    protected $unitPrice = 0;

    /**
     * @var int
     */
    protected $total = 0;

    /**
     * @var bool
     */
    protected $immutable = false;

    /**
     * @var Collection|OrderItemUnitInterface[]
     */
    protected $units;

    /**
     * @var int
     */
    protected $unitsTotal = 0;

    /**
     * @var Collection|AdjustmentInterface[]
     */
    protected $adjustments;

    /**
     * @var int
     */
    protected $adjustmentsTotal = 0;

    public function __construct()
    {
        $this->adjustments = new ArrayCollection();
        $this->units = new ArrayCollection();
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
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        if (1 > $quantity) {
            throw new \OutOfRangeException('Quantity must be greater than 0.');
        }

        $this->quantity = $quantity;
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
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitPrice($unitPrice)
    {
        if (!is_int($unitPrice)) {
            throw new \InvalidArgumentException('Unit price must be an integer.');
        }

        $this->unitPrice = $unitPrice;
        $this->recalculateUnitsTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     *  Recalculates total after units total or adjustments total change.
     */
    protected function recalculateTotal()
    {
        $this->total = $this->unitsTotal + $this->adjustmentsTotal;

        if ($this->total < 0) {
            $this->total = 0;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function recalculateAdjustmentsTotal()
    {
        $this->adjustmentsTotal = 0;

        foreach ($this->adjustments as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $this->adjustmentsTotal += $adjustment->getAmount();
            }
        }

        $this->recalculateTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function recalculateUnitsTotal()
    {
        $this->unitsTotal = 0;

        foreach ($this->units as $unit) {
            $this->unitsTotal += $unit->getTotal();
        }

        $this->recalculateTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function equals(OrderItemInterface $orderItem)
    {
        return $this === $orderItem;
    }

    /**
     * {@inheritdoc}
     */
    public function merge(OrderItemInterface $orderItem, $throwOnInvalid = true)
    {
        if ($throwOnInvalid && !$orderItem->equals($this)) {
            throw new \LogicException('Given item cannot be merged.');
        }

        if ($this !== $orderItem) {
            $this->quantity += $orderItem->getQuantity();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function isImmutable()
    {
        return $this->immutable;
    }

    /**
     * {@inheritdoc}
     */
    public function setImmutable($immutable)
    {
        $this->immutable = (bool) $immutable;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnits()
    {
        return $this->units;
    }

    /**
     * {@inheritdoc}
     */
    public function addUnit(OrderItemUnitInterface $unit)
    {
        if ($this !== $unit->getOrderItem()) {
            throw new \LogicException('This order item unit is assigned to a different order item.');
        }

        if (!$this->hasUnit($unit)) {
            $this->units->add($unit);
            $this->unitsTotal += $unit->getTotal();
            $this->recalculateTotal();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeUnit(OrderItemUnitInterface $unit)
    {
        if ($this->hasUnit($unit)) {
            $this->units->removeElement($unit);
            $this->unitsTotal -= $unit->getTotal();
            $this->recalculateTotal();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasUnit(OrderItemUnitInterface $unit)
    {
        return $this->units->contains($unit);
    }
    /**
     * {@inheritdoc}
     */
    public function getAdjustments($type = null)
    {
        if (null === $type) {
            return $this->adjustments;
        }

        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) use ($type) {
            return $type === $adjustment->getType();
        });
    }

    /**
     * {@inheritdoc}
     */
    public function addAdjustment(AdjustmentInterface $adjustment)
    {
        if (!$this->hasAdjustment($adjustment)) {
            $adjustment->setAdjustable($this);
            $this->adjustments->add($adjustment);
            $this->addToAdjustmentsTotal($adjustment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustment(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->isLocked() && $this->hasAdjustment($adjustment)) {
            $adjustment->setAdjustable(null);
            $this->adjustments->removeElement($adjustment);
            $this->subtractFromAdjustmentsTotal($adjustment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAdjustment(AdjustmentInterface $adjustment)
    {
        return $this->adjustments->contains($adjustment);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentsTotal($type = null)
    {
        if (null === $type) {
            return $this->adjustmentsTotal;
        }

        $total = 0;
        foreach ($this->getAdjustments($type) as $adjustment) {
            $total += $adjustment->getAmount();
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentsTotalRecursively($type = null)
    {
        $total = $this->getAdjustmentsTotal($type);
        foreach ($this->units as $unit) {
            $total += $unit->getAdjustmentsTotal($type);
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustments($type)
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            if ($adjustment->isLocked()) {
                continue;
            }

            $this->removeAdjustment($adjustment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearAdjustments()
    {
        $this->adjustments->clear();
        $this->recalculateAdjustmentsTotal();
    }

    protected function addToAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal += $adjustment->getAmount();
            $this->recalculateTotal();
        }
    }

    protected function subtractFromAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal -= $adjustment->getAmount();
            $this->recalculateTotal();
        }
    }
}
