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
 */
class OrderItemUnit implements OrderItemUnitInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var int
     */
    protected $total = 0;

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

    public function __construct()
    {
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
        return $this->total;
    }

    public function calculateTotal()
    {
        $this->calculateAdjustmentsTotal();

        $this->total = $this->orderItem->getUnitPrice() + $this->adjustmentsTotal;
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
    public function calculateAdjustmentsTotal()
    {
        $this->adjustmentsTotal = 0;

        foreach ($this->adjustments as $adjustment) {
            if (!$adjustment->isNeutral()) {
                $this->adjustmentsTotal += $adjustment->getAmount();
            }
        }
    }
}
