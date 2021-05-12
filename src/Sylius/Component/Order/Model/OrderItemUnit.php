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

class OrderItemUnit implements OrderItemUnitInterface
{
    /** @var int */
    protected $id;

    /** @var OrderItemInterface */
    protected $orderItem;

    /**
     * @var Collection|AdjustmentInterface[]
     *
     * @psalm-var Collection<array-key, AdjustmentInterface>
     */
    protected $adjustments;

    /** @var int */
    protected $adjustmentsTotal = 0;

    public function __construct(OrderItemInterface $orderItem)
    {
        /** @var ArrayCollection<array-key, AdjustmentInterface> $this->adjustments */
        $this->adjustments = new ArrayCollection();

        $this->orderItem = $orderItem;
        $this->orderItem->addUnit($this);
    }

    public function getId()
    {
        return $this->id;
    }

    public function getTotal(): int
    {
        $total = $this->orderItem->getUnitPrice() + $this->adjustmentsTotal;

        if ($total < 0) {
            return 0;
        }

        return $total;
    }

    public function getOrderItem(): OrderItemInterface
    {
        return $this->orderItem;
    }

    public function getAdjustments(?string $type = null): Collection
    {
        if (null === $type) {
            return $this->adjustments;
        }

        return $this->adjustments->filter(function (AdjustmentInterface $adjustment) use ($type): bool {
            return $type === $adjustment->getType();
        });
    }

    public function addAdjustment(AdjustmentInterface $adjustment): void
    {
        if ($this->hasAdjustment($adjustment)) {
            return;
        }

        $this->adjustments->add($adjustment);
        $this->addToAdjustmentsTotal($adjustment);
        $this->orderItem->recalculateUnitsTotal();
        $adjustment->setAdjustable($this);
    }

    public function removeAdjustment(AdjustmentInterface $adjustment): void
    {
        if ($adjustment->isLocked() || !$this->hasAdjustment($adjustment)) {
            return;
        }

        $this->adjustments->removeElement($adjustment);
        $this->subtractFromAdjustmentsTotal($adjustment);
        $this->orderItem->recalculateUnitsTotal();
        $adjustment->setAdjustable(null);
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

    public function removeAdjustments(?string $type = null): void
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            $this->removeAdjustment($adjustment);
        }

        $this->recalculateAdjustmentsTotal();
    }

    public function recalculateAdjustmentsTotal(): void
    {
        $this->adjustmentsTotal = 0;

        foreach ($this->adjustments as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $this->adjustmentsTotal += $adjustment->getAmount();
            }
        }
    }

    protected function addToAdjustmentsTotal(AdjustmentInterface $adjustment): void
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal += $adjustment->getAmount();
        }
    }

    protected function subtractFromAdjustmentsTotal(AdjustmentInterface $adjustment): void
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal -= $adjustment->getAmount();
        }
    }
}
