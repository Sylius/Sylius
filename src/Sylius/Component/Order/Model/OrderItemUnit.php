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
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class OrderItemUnit implements OrderItemUnitInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var OrderItemInterface
     */
    protected $orderItem;

    /**
     * @var Collection|AdjustmentInterface[]
     */
    protected $adjustments;

    /**
     * @var int
     */
    protected $adjustmentsTotal = 0;

    /**
     * @param OrderItemInterface $orderItem
     */
    public function __construct(OrderItemInterface $orderItem)
    {
        $this->orderItem = $orderItem;
        $this->orderItem->addUnit($this);

        $this->adjustments = new ArrayCollection();
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
    public function getTotal()
    {
        $total = $this->orderItem->getUnitPrice() + $this->adjustmentsTotal;

        if ($total < 0) {
            return 0;
        }

        return $total;
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
        if ($this->hasAdjustment($adjustment)) {
            return;
        }

        $this->adjustments->add($adjustment);
        $this->addToAdjustmentsTotal($adjustment);
        $this->orderItem->recalculateUnitsTotal();
        $adjustment->setAdjustable($this);
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustment(AdjustmentInterface $adjustment)
    {
        if ($adjustment->isLocked() || !$this->hasAdjustment($adjustment)) {
            return;
        }

        $this->adjustments->removeElement($adjustment);
        $this->subtractFromAdjustmentsTotal($adjustment);
        $this->orderItem->recalculateUnitsTotal();
        $adjustment->setAdjustable(null);
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
            if (!$adjustment->isNeutral()) {
                $total += $adjustment->getAmount();
            }
        }

        return $total;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustments($type)
    {
        foreach ($this->getAdjustments($type) as $adjustment) {
            $this->removeAdjustment($adjustment);
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
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function addToAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal += $adjustment->getAmount();
        }
    }

    /**
     * @param AdjustmentInterface $adjustment
     */
    protected function subtractFromAdjustmentsTotal(AdjustmentInterface $adjustment)
    {
        if (!$adjustment->isNeutral()) {
            $this->adjustmentsTotal -= $adjustment->getAmount();
        }
    }
}
