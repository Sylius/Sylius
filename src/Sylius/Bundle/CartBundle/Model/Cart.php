<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;

/**
 * Model for carts.
 * All driver entities and documents should extend this class or implement
 * proper interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class Cart implements CartInterface
{
    /**
     * Id.
     *
     * @var integer
     */
    protected $id;

    /**
     * Items in cart.
     *
     * @var Collection
     */
    protected $items;

    /**
     * Total items count.
     *
     * @var integer
     */
    protected $totalItems;

    /**
     * Total quantity of items.
     *
     * @var integer
     */
    protected $totalQuantity;

    /**
     * Total value.
     *
     * @var float
     */
    protected $total;

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
        $this->totalItems = 0;
        $this->totalQuantity = 0;
        $this->total = 0;
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
        return $this->totalItems;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalItems($totalItems)
    {
        if (0 > $totalItems) {
            throw new \OutOfRangeException('Total items must not be less than 0');
        }

        $this->totalItems = $totalItems;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function changeTotalItems($amount)
    {
        $this->totalItems += $amount;

        if (0 > $this->totalItems) {
            $this->totalItems = 0;
        }

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotalQuantity()
    {
        return $this->totalQuantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotalQuantity($totalQuantity)
    {
        if (0 > $totalQuantity) {
            throw new \OutOfRangeException('Total quantity must not be less than 0');
        }

        $this->totalQuantity = $totalQuantity;
    }

    /**
     * {@inheritdoc}
     */
    public function changeTotalQuantity($amount)
    {
        $this->totalQuantity += $amount;

        if (0 > $this->totalQuantity) {
            $this->totalQuantity = 0;
        }
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
    public function getItems()
    {
        return $this->items;
    }

    /**
     * {@inheritdoc}
     */
    public function setItems(Collection $items)
    {
        foreach($this->items as $item){
            $this->removeItem($item);
        }

        foreach($items as $item){
            $this->addItem($item);
        }

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
    public function addItem(CartItemInterface $item)
    {
        if ($this->items->contains($item)) {
            return $this;
        }

        foreach ($this->items as $existingItem) {
            if ($item->equals($existingItem)) {
                $existingItem->setQuantity($existingItem->getQuantity() + $item->getQuantity());

                return $this;
            }
        }

        $this->items->add($item);
        $item->setCart($this);

        return $this;
    }


    /**
     * {@inheritdoc}
     */
    public function removeItem(CartItemInterface $item)
    {
        if ($this->items->contains($item)) {
            $this->items->removeElement($item);

            $item->setCart(null);
        }

        return $this;
    }

    public function hasItem(CartItemInterface $item)
    {
        return $this->items->contains($item);
    }

    public function getTotal()
    {
        return $this->total;
    }

    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    public function calculateTotal()
    {
        // Reset total.
        $this->total = 0;

        foreach ($this->items as $item) {
            $item->calculateTotal();

            $this->total += $item->getTotal();
        }

        return $this;
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
