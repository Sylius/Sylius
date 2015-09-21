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
 * Model for order line items.
 *
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
     * @var Collection|AdjustmentInterface[]
     */
    protected $adjustments;

    /**
     * @var int
     */
    protected $adjustmentsTotal = 0;

    /**
     * @var int
     */
    protected $total = 0;

    /**
     * Order item is immutable?
     *
     * @var bool
     */
    protected $immutable = false;

    /**
     * Constructor.
     */
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
            return $type === $adjustment->getLabel();
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

            $adjustment->setAdjustable(null);
            $this->adjustments->removeElement($adjustment);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function clearAdjustments()
    {
        return $this->adjustments->clear();
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
    public function setTotal($total)
    {
        if (!is_int($total)) {
            throw new \InvalidArgumentException('Total must be an integer.');
        }
        $this->total = $total;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateTotal()
    {
        $this->calculateAdjustmentsTotal();

        $this->total = ($this->quantity * $this->unitPrice) + $this->adjustmentsTotal;

        if ($this->total < 0) {
            $this->total = 0;
        }
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
}
