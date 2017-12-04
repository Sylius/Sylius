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
use Sylius\Component\Resource\Model\TimestampableTrait;

class Order implements OrderInterface
{
    use TimestampableTrait;

    /**
     * @var mixed
     */
    protected $id;

    /**
     * @var \DateTimeInterface|null
     */
    protected $checkoutCompletedAt;

    /**
     * @var string|null
     */
    protected $number;

    /**
     * @var string|null
     */
    protected $notes;

    /**
     * @var Collection|OrderItemInterface[]
     */
    protected $items;

    /**
     * @var int
     */
    protected $itemsTotal = 0;

    /**
     * @var Collection|AdjustmentInterface[]
     */
    protected $adjustments;

    /**
     * @var int
     */
    protected $adjustmentsTotal = 0;

    /**
     * Items total + adjustments total.
     *
     * @var int
     */
    protected $total = 0;

    /**
     * @var string
     */
    protected $state = OrderInterface::STATE_CART;

    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->adjustments = new ArrayCollection();
        $this->createdAt = new \DateTime();
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
    public function getCheckoutCompletedAt(): ?\DateTimeInterface
    {
        return $this->checkoutCompletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCheckoutCompletedAt(?\DateTimeInterface $checkoutCompletedAt): void
    {
        $this->checkoutCompletedAt = $checkoutCompletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isCheckoutCompleted(): bool
    {
        return null !== $this->checkoutCompletedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function completeCheckout(): void
    {
        $this->checkoutCompletedAt = new \DateTime();
    }

    /**
     * {@inheritdoc}
     */
    public function getNumber(): ?string
    {
        return $this->number;
    }

    /**
     * {@inheritdoc}
     */
    public function setNumber(?string $number): void
    {
        $this->number = $number;
    }

    /**
     * {@inheritdoc}
     */
    public function getNotes(): ?string
    {
        return $this->notes;
    }

    /**
     * {@inheritdoc}
     */
    public function setNotes(?string $notes): void
    {
        $this->notes = $notes;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function clearItems(): void
    {
        $this->items->clear();

        $this->recalculateItemsTotal();
    }

    /**
     * {@inheritdoc}
     */
    public function countItems(): int
    {
        return $this->items->count();
    }

    /**
     * {@inheritdoc}
     */
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

    /**
     * {@inheritdoc}
     */
    public function removeItem(OrderItemInterface $item): void
    {
        if ($this->hasItem($item)) {
            $this->items->removeElement($item);
            $this->itemsTotal -= $item->getTotal();
            $this->recalculateTotal();
            $item->setOrder(null);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem(OrderItemInterface $item): bool
    {
        return $this->items->contains($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsTotal(): int
    {
        return $this->itemsTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function recalculateItemsTotal(): void
    {
        $this->itemsTotal = 0;
        foreach ($this->items as $item) {
            $this->itemsTotal += $item->getTotal();
        }

        $this->recalculateTotal();
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
    public function getTotalQuantity(): int
    {
        $quantity = 0;

        foreach ($this->items as $item) {
            $quantity += $item->getQuantity();
        }

        return $quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getState(): string
    {
        return $this->state;
    }

    /**
     * {@inheritdoc}
     */
    public function setState(string $state): void
    {
        $this->state = $state;
    }

    /**
     * {@inheritdoc}
     */
    public function isEmpty(): bool
    {
        return $this->items->isEmpty();
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
        foreach ($this->items as $item) {
            foreach ($item->getAdjustmentsRecursively($type) as $adjustment) {
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
            if ($adjustment->isLocked()) {
                continue;
            }

            $this->removeAdjustment($adjustment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustmentsRecursively(?string $type = null): void
    {
        $this->removeAdjustments($type);
        foreach ($this->items as $item) {
            $item->removeAdjustmentsRecursively($type);
        }
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
     * Items total + Adjustments total.
     */
    protected function recalculateTotal(): void
    {
        $this->total = $this->itemsTotal + $this->adjustmentsTotal;

        if ($this->total < 0) {
            $this->total = 0;
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
