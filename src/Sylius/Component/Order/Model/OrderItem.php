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

use function round;

class OrderItem implements OrderItemInterface
{
    /** @var mixed */
    protected $id;

    /** @var OrderInterface|null */
    protected $order;

    /** @var int */
    protected $quantity = 0;

    /** @var float */
    protected $unitPrice = 0.0;

    protected ?float $originalUnitPrice = 0.0;

    /** @var float */
    protected $total = 0.0;

    /** @var bool */
    protected $immutable = false;

    /** @var Collection<array-key, OrderItemUnitInterface> */
    protected $units;

    /** @var float */
    protected $unitsTotal = 0.0;

    /** @var Collection<array-key, AdjustmentInterface> */
    protected $adjustments;

    /** @var float */
    protected $adjustmentsTotal = 0.0;

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

    public function getUnitPrice(): float
    {
        return $this->unitPrice;
    }

    public function setUnitPrice(float $unitPrice): void
    {
        $this->unitPrice = $unitPrice;
        $this->recalculateUnitsTotal();
    }

    public function getOriginalUnitPrice(): ?float
    {
        return $this->originalUnitPrice;
    }

    public function setOriginalUnitPrice(?float $originalUnitPrice): void
    {
        $this->originalUnitPrice = $originalUnitPrice;
    }

    public function getTotal(): float
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
        $this->unitsTotal = (int) round($this->getUnitPrice() * count($this->units));

        foreach ($this->units as $unit) {
            $this->unitsTotal += $unit->getAdjustmentsTotal();
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

    public function getAdjustmentsTotal(?string $type = null): float
    {
        if (null === $type) {
            return $this->adjustmentsTotal;
        }

        $total = 0.0;
        foreach ($this->getAdjustments($type) as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $total += $adjustment->getAmount();
            }
        }

        return $total;
    }

    public function getAdjustmentsTotalRecursively(?string $type = null): float
    {
        $total = 0.0;

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
