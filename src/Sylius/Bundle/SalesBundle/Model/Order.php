<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Bundle\ResourceBundle\Model\TimestampableInterface;

/**
 * Model for orders.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Order implements OrderInterface, TimestampableInterface
{
    /**
     * Id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Order number.
     *
     * @var string
     */
    protected $number;

    /**
     * Items in order.
     *
     * @var Collection
     */
    protected $items;

    /**
     * Items total.
     *
     * @var integer
     */
    protected $itemsTotal;

    /**
     * Adjustments.
     *
     * @var Collection
     */
    protected $adjustments;

    /**
     * Adjustments total.
     *
     * @var integer
     */
    protected $adjustmentsTotal;

    /**
     * Calculated total.
     * Items total + adjustments total.
     *
     * @var integer
     */
    protected $total;

    /**
     * Whether order was confirmed.
     *
     * @var Boolean
     */
    protected $confirmed;

    /**
     * Confirmation token.
     *
     * @var string
     */
    protected $confirmationToken;

    /**
     * Creation time.
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * Modification time.
     *
     * @var \DateTime
     */
    protected $updatedAt;

    /**
     * Is cart locked?
     * Locked carts should not be removed
     * even if expired.
     *
     * @var Boolean
     */
    protected $locked;

    /**
     * Expiration time.
     *
     * @var \DateTime
     */
    protected $expiresAt;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->items = new ArrayCollection();
        $this->itemsTotal = 0;
        $this->adjustments = new ArrayCollection();
        $this->adjustmentsTotal = 0;
        $this->total = 0;
        $this->confirmed = true;
        $this->createdAt = new \DateTime();
        $this->locked = false;
        $this->incrementExpiresAt();
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
    public function getNumber()
    {
        return $this->number;
    }

    /**
     * {@inheritdoc}
     */
    public function setNumber($number)
    {
        $this->number = $number;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function isConfirmed()
    {
        return $this->confirmed;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmed($confirmed)
    {
        $this->confirmed = (Boolean) $confirmed;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfirmationToken()
    {
        return $this->confirmationToken;
    }

    /**
     * {@inheritdoc}
     */
    public function setConfirmationToken($confirmationToken)
    {
        $this->confirmationToken = $confirmationToken;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(Collection $items)
    {
        $this->items = $items;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function clearItems()
    {
        $this->items->clear();

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function countItems()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function addItem(OrderItemInterface $item)
    {
        if ($this->hasItem($item)) {
            return $this;
        }

        foreach ($this->items as $existingItem) {
            if ($item->equals($existingItem)) {
                $existingItem->setQuantity($existingItem->getQuantity() + $item->getQuantity());

                return $this;
            }
        }

        $item->setOrder($this);
        $this->items->add($item);

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeItem(OrderItemInterface $item)
    {
        if ($this->hasItem($item)) {
            $item->setOrder(null);
            $this->items->removeElement($item);
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function hasItem(OrderItemInterface $item)
    {
        return $this->items->contains($item);
    }

    /**
     * {@inheritdoc}
     */
    public function getItemsTotal()
    {
        return $this->itemsTotal;
    }

    /**
     * {@inheritdoc}
     */
    public function setItemsTotal($itemsTotal)
    {
        $this->itemsTotal = $itemsTotal;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateItemsTotal()
    {
        $itemsTotal = 0;

        foreach ($this->items as $item) {
            $item->calculateTotal();

            $itemsTotal += $item->getTotal();
        }

        $this->itemsTotal = $itemsTotal;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getAdjustments()
    {
        return $this->adjustments;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function removeAdjustment(AdjustmentInterface $adjustment)
    {
        if ($this->hasAdjustment($adjustment)) {
            $adjustment->setAdjustable(null);
            $this->adjustments->removeElement($adjustment);
        }

        return $this;
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
    public function getAdjustmentsTotal()
    {
        return $this->adjustmentsTotal;
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
        $this->total = $total;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateTotal()
    {
        $this->calculateItemsTotal();
        $this->calculateAdjustmentsTotal();

        $this->total = $this->itemsTotal + $this->adjustmentsTotal;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setCreatedAt(\DateTime $createdAt)
    {
        $this->createdAt = $createdAt;
    }

    /**
     * {@inheritdoc}
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setUpdatedAt(\DateTime $updatedAt)
    {
        $this->updatedAt = $updatedAt;
    }

    /**
     * {@inheritdoc}
     */
    public function isLocked()
    {
        return $this->locked;
    }

    /**
     * {@inheritdoc}
     */
    public function setLocked($locked)
    {
        $this->locked = $locked;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalItems()
    {
        return count($this->items);
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalQuantity()
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
    public function isEmpty()
    {
        return 0 === $this->countItems();
    }

    /**
     * {@inheritdoc}
     */
    public function isExpired()
    {
        return $this->getExpiresAt() < new \DateTime('now');
    }

    /**
     * {@inheritdoc}
     */
    public function getExpiresAt()
    {
        return $this->expiresAt;
    }

    /**
     * {@inheritdoc}
     */
    public function setExpiresAt(\DateTime $expiresAt = null)
    {
        $this->expiresAt = $expiresAt;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function incrementExpiresAt()
    {
        $expiresAt = new \DateTime();
        $expiresAt->add(new \DateInterval('PT3H'));

        $this->expiresAt = $expiresAt;

        return $this;
    }
}
