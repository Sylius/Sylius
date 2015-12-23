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
    use Adjustable;

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
     * @var Collection|OrderItemUnitInterface[]
     */
    protected $itemUnits;

    /**
     * Constructor.
     */
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
        }
    }

    /**
     * {@inheritdoc}
     */
    public function removeItemUnit(OrderItemUnitInterface $itemUnit)
    {
        $itemUnit->setOrderItem(null);
        $this->itemUnits->removeElement($itemUnit);
    }

    /**
     * {@inheritdoc}
     */
    public function hasItemUnit(OrderItemUnitInterface $itemUnit)
    {
        return $this->itemUnits->contains($itemUnit);
    }
}
