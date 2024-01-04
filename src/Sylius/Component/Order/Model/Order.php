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
use Sylius\Component\Resource\Model\TimestampableTrait;

class Order implements OrderInterface
{
    use TimestampableTrait;

    /** @var mixed */
    protected $id;

    /** @var \DateTimeInterface|null */
    protected $checkoutCompletedAt;

    /** @var string|null */
    protected $number;

    /** @var string|null */
    protected $notes;

    /** @var Collection<array-key, OrderItemInterface> */
    protected $items;

    /** @var int */
    protected $itemsTotal = 0;

    /** @var Collection<array-key, AdjustmentInterface> */
    protected $adjustments;

    /** @var int */
    protected $adjustmentsTotal = 0;

    /**
     * Items total + adjustments total.
     *
     * @var int
     */
    protected $total = 0;

    /** @var string */
    protected $state = OrderInterface::STATE_CART;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->adjustments = new ArrayCollection();

        $this->createdAt = new \DateTime();
    }

    public function getId()
    {
        return $this->id;
    }

    public function getCheckoutCompletedAt(): ?\DateTimeInterface
    {
        return $this->checkoutCompletedAt;
    }

    public function setCheckoutCompletedAt(?\DateTimeInterface $checkoutCompletedAt): void
    {
        $this->checkoutCompletedAt = $checkoutCompletedAt;
    }

    public function isCheckoutCompleted(): bool
    {
        return null !== $this->checkoutCompletedAt;
    }

    public function completeCheckout(): void
    {
        $this->checkoutCompletedAt = new \DateTime();
    }

    public function getNumber(): ?string
    {
        return $this->number;
    }

    public function setNumber(?string $number): void
    {
        $this->number = $number;
    }

    public function getNotes(): ?string
    {
        return $this->notes;
    }

    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    public function getItems(): Collection
    {
        return $this->items;
    }

    public function clearItems(): void
    {
        $this->items->clear();

        $this->recalculateItemsTotal();
    }

    public function countItems(): int
    {
        return $this->items->count();
    }

    public function addItem(OrderItemInterface $item): void
    {
        if ($this->hasItem($item)) {
            return;
        }

        $this->itemsTotal += $item->getTotal();
        $this->items->add($item);
        $item->setOrder($this);

        $this->recalculateTotal();
    }

    public function removeItem(OrderItemInterface $item): void
    {
        if ($this->hasItem($item)) {
            $this->items->removeElement($item);
            $this->itemsTotal -= $item->getTotal();
            $this->recalculateTotal();
            $item->setOrder(null);
        }
    }

    public function hasItem(OrderItemInterface $item): bool
    {
        return $this->items->contains($item);
    }

    public function getItemsTotal(): int
    {
        return $this->itemsTotal;
    }

    public function recalculateItemsTotal(): void
    {
        $this->itemsTotal = 0;
        foreach ($this->items as $item) {
            $this->itemsTotal += $item->getTotal();
        }

        $this->recalculateTotal();
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function getTotalQuantity(): int
    {
        $quantity = 0;

        foreach ($this->items as $item) {
            $quantity += $item->getQuantity();
        }

        return $quantity;
    }

    public function getState(): string
    {
        return $this->state;
    }

    public function setState(string $state): void
    {
        $this->state = $state;
    }

    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
    }

    public function getAdjustments(?string $type = null): Collection
    {
        if (null === $type) {
            return $this->adjustments;
        }

        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) use ($type) {
            return $type === $adjustment->getType();
        });
    }

    public function getAdjustmentsRecursively(?string $type = null): Collection
    {
        $adjustments = clone $this->getAdjustments($type);
        foreach ($this->items as $item) {
            foreach ($item->getAdjustmentsRecursively($type) as $adjustment) {
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
            if ($adjustment->isLocked()) {
                continue;
            }

            $this->removeAdjustment($adjustment);
        }

        $this->recalculateAdjustmentsTotal();
    }

    public function removeAdjustmentsRecursively(?string $type = null): void
    {
        $this->removeAdjustments($type);
        foreach ($this->items as $item) {
            $item->removeAdjustmentsRecursively($type);
        }
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

    public function canBeProcessed(): bool
    {
        return $this->state === self::STATE_CART;
    }

    /**
     * Items total + Adjustments total.
     */
    protected function recalculateTotal(): void
    {
        $this->total = $this->itemsTotal + $this->adjustmentsTotal;

        if ($this->total < 0) {
            $this->total = 0;
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
