<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Order\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

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
    protected $quantity = 0;

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
    public function getQuantity(): int
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

    /**
     * {@inheritdoc}
     */
    public function setOrder(?OrderInterface $order): void
    {
        $currentOrder = $this->getOrder();
        if ($currentOrder === $order) {
            return;
        }

        $this->order = null;

        if (null !== $currentOrder) {
            $currentOrder->removeItem($this);
        }

        if (null === $order) {
            return;
        }

        $this->order = $order;

        if (!$order->hasItem($this)) {
            $order->addItem($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
        $this->recalculateUnitsTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal(): int
    {
        return $this->total;
    }

    /**
     * {@inheritdoc}
     */
    public function recalculateAdjustmentsTotal(): void
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
    public function recalculateUnitsTotal(): void
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
    public function equals(OrderItemInterface $orderItem): bool
    {
        return $this === $orderItem;
    }

    /**
     * {@inheritdoc}
     */
    public function isImmutable(): bool
    {
        return $this->immutable;
    }

    /**
     * {@inheritdoc}
     */
    public function setImmutable(bool $immutable): void
    {
        $this->immutable = $immutable;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnits(): Collection
    {
        return $this->units;
    }

    /**
     * {@inheritdoc}
     */
    public function addUnit(OrderItemUnitInterface $unit): void
    {
        if ($this !== $unit->getOrderItem()) {
            throw new \LogicException('This order item unit is assigned to a different order item.');
        }

        if (!$this->hasUnit($unit)) {
            $this->units->add($unit);

            ++$this->quantity;
            $this->unitsTotal += $unit->getTotal();
            $this->recalculateTotal();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeUnit(OrderItemUnitInterface $unit): void
    {
        if ($this->hasUnit($unit)) {
            $this->units->removeElement($unit);

            --$this->quantity;
            $this->unitsTotal -= $unit->getTotal();
            $this->recalculateTotal();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasUnit(OrderItemUnitInterface $unit): bool
    {
        return $this->units->contains($unit);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustments(?string $type = null): Collection
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
    public function getAdjustmentsRecursively(?string $type = null): Collection
    {
        $adjustments = clone $this->getAdjustments($type);

        foreach ($this->units as $unit) {
            foreach ($unit->getAdjustments($type) as $adjustment) {
                $adjustments->add($adjustment);
            }
        }

        return $adjustments;
    }

    /**
     * {@inheritdoc}
     */
    public function addAdjustment(AdjustmentInterface $adjustment): void
    {
        if (!$this->hasAdjustment($adjustment)) {
            $this->adjustments->add($adjustment);
            $this->addToAdjustmentsTotal($adjustment);
            $adjustment->setAdjustable($this);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustment(AdjustmentInterface $adjustment): void
    {
        if (!$adjustment->isLocked() && $this->hasAdjustment($adjustment)) {
            $this->adjustments->removeElement($adjustment);
            $this->subtractFromAdjustmentsTotal($adjustment);
            $adjustment->setAdjustable(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasAdjustment(AdjustmentInterface $adjustment): bool
    {
        return $this->adjustments->contains($adjustment);
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentsTotal(?string $type = null): int
    {
        if (null === $type) {
            return $this->adjustmentsTotal;
        }

        $total = 0;
        foreach ($this->getAdjustments($type) as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $total += $adjustment->getAmount();
            }
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustmentsTotalRecursively(?string $type = null): int
    {
        $total = 0;

        foreach ($this->getAdjustmentsRecursively($type) as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $total += $adjustment->getAmount();
            }
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustments(?string $type = null): void
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            $this->removeAdjustment($adjustment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustmentsRecursively(?string $type = null): void
    {
        $this->removeAdjustments($type);
        foreach ($this->units as $unit) {
            $unit->removeAdjustments($type);
        }
    }

    /**
     * Recalculates total after units total or adjustments total change.
     */
    protected function recalculateTotal(): void
    {
        $this->total = $this->unitsTotal + $this->adjustmentsTotal;

        if ($this->total < 0) {
            $this->total = 0;
        }

        if (null !== $this->order) {
            $this->order->recalculateItemsTotal();
        }
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function addToAdjustmentsTotal(AdjustmentInterface $adjustment): void
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal += $adjustment->getAmount();
            $this->recalculateTotal();
        }
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function subtractFromAdjustmentsTotal(AdjustmentInterface $adjustment): void
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal -= $adjustment->getAmount();
            $this->recalculateTotal();
        }
    }
}
