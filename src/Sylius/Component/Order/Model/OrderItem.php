<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
    /** @var mixed */
    protected $id;

    /** @var OrderInterface|null */
    protected $order;

    /** @var int */
    protected $quantity = 0;

    /** @var int */
    protected $unitPrice = 0;

    protected ?int $originalUnitPrice = 0;

    /** @var int */
    protected $total = 0;

    /** @var bool */
    protected $immutable = false;

    /** @var Collection<array-key, OrderItemUnitInterface> */
    protected $units;

    /** @var int */
    protected $unitsTotal = 0;

    /** @var Collection<array-key, AdjustmentInterface> */
    protected $adjustments;

    /** @var int */
    protected $adjustmentsTotal = 0;

    public function __construct()
    {
        $this->adjustments = new ArrayCollection();
        $this->units = new ArrayCollection();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getQuantity(): int
    {
        return $this->quantity;
    }

    public function getOrder(): ?OrderInterface
    {
        return $this->order;
    }

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

    public function getUnitPrice(): int
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(int $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
        $this->recalculateUnitsTotal();
    }

    public function getOriginalUnitPrice(): ?int
    {
        return $this->originalUnitPrice;
    }

    public function setOriginalUnitPrice(?int $originalUnitPrice): void
    {
        $this->originalUnitPrice = $originalUnitPrice;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

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

    public function recalculateUnitsTotal(): void
    {
        $this->unitsTotal = 0;

        foreach ($this->units as $unit) {
            $this->unitsTotal += $unit->getTotal();
        }

        $this->recalculateTotal();
    }

    public function equals(OrderItemInterface $orderItem): bool
    {
        return $this === $orderItem;
    }

    public function isImmutable(): bool
    {
        return $this->immutable;
    }

    public function setImmutable(bool $immutable): void
    {
        $this->immutable = $immutable;
    }

    public function getUnits(): Collection
    {
        return $this->units;
    }

    public function addUnit(OrderItemUnitInterface $itemUnit): void
    {
        if ($this !== $itemUnit->getOrderItem()) {
            throw new \LogicException('This order item unit is assigned to a different order item.');
        }

        if (!$this->hasUnit($itemUnit)) {
            $this->units->add($itemUnit);

            ++$this->quantity;
            $this->unitsTotal += $itemUnit->getTotal();
            $this->recalculateTotal();
        }
    }

    public function removeUnit(OrderItemUnitInterface $itemUnit): void
    {
        if ($this->hasUnit($itemUnit)) {
            $this->units->removeElement($itemUnit);

            --$this->quantity;
            $this->unitsTotal -= $itemUnit->getTotal();
            $this->recalculateTotal();
        }
    }

    public function hasUnit(OrderItemUnitInterface $itemUnit): bool
    {
        return $this->units->contains($itemUnit);
    }

    public function getAdjustments(?string $type = null): Collection
    {
        if (null === $type) {
            return $this->adjustments;
        }

        return $this->adjustments->filter(static function (AdjustmentInterface $adjustment) use ($type) {
            return $type === $adjustment->getType();
        });
    }

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

    public function addAdjustment(AdjustmentInterface $adjustment): void
    {
        if (!$this->hasAdjustment($adjustment)) {
            $this->adjustments->add($adjustment);
            $this->addToAdjustmentsTotal($adjustment);
            $adjustment->setAdjustable($this);
            $this->recalculateAdjustmentsTotal();
        }
    }

    public function removeAdjustment(AdjustmentInterface $adjustment): void
    {
        if (!$adjustment->isLocked() && $this->hasAdjustment($adjustment)) {
            $this->adjustments->removeElement($adjustment);
            $this->subtractFromAdjustmentsTotal($adjustment);
            $adjustment->setAdjustable(null);
            $this->recalculateAdjustmentsTotal();
        }
    }

    public function hasAdjustment(AdjustmentInterface $adjustment): bool
    {
        return $this->adjustments->contains($adjustment);
    }

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

    public function removeAdjustments(?string $type = null): void
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            $this->removeAdjustment($adjustment);
        }

        $this->recalculateAdjustmentsTotal();
    }

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

    protected function addToAdjustmentsTotal(AdjustmentInterface $adjustment): void
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal += $adjustment->getAmount();
            $this->recalculateTotal();
        }
    }

    protected function subtractFromAdjustmentsTotal(AdjustmentInterface $adjustment): void
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal -= $adjustment->getAmount();
            $this->recalculateTotal();
        }
    }
}
