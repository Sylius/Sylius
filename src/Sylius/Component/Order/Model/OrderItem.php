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
    protected $itemUnits;

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
        $this->itemUnits = new ArrayCollection();
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
        if (0 > $quantity) {
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

        foreach ($this->itemUnits as $unitPrice) {
            $unitPrice->calculateTotal();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return $this->total;
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
            throw new \RuntimeException('Given item cannot be merged.');
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
    public function getItemUnits()
    {
        return $this->itemUnits;
    }

    /**
     * {@inheritdoc}
     */
    public function addItemUnit(OrderItemUnitInterface $itemUnit)
    {
        if (!$this->hasItemUnit($itemUnit)) {
            $itemUnit->setOrderItem($this);
            $this->itemUnits->add($itemUnit);

            $this->calculateTotal();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeItemUnit(OrderItemUnitInterface $itemUnit)
    {
        $itemUnit->setOrderItem(null);
        $this->itemUnits->removeElement($itemUnit);

        $this->calculateTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemUnit(OrderItemUnitInterface $itemUnit)
    {
        return $this->itemUnits->contains($itemUnit);
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

            $this->calculateTotal();
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

            $this->calculateTotal();
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
    }

    /**
     * {@inheritdoc}
     */
    protected function calculateAdjustmentsTotal()
    {
        $this->adjustmentsTotal = 0;

        foreach ($this->adjustments as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $this->adjustmentsTotal += $adjustment->getAmount();
            }
        }
    }

    protected function calculateTotal()
    {
        $this->calculateAdjustmentsTotal();

        $this->total = $this->adjustmentsTotal;
        foreach ($this->itemUnits as $unit) {
            $this->total += $unit->getTotal();
        }

        if ($this->total < 0) {
            $this->total = 0;
        }
    }
}
