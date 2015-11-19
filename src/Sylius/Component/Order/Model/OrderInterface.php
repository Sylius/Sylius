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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Resource\Model\ResourceInterface;
use Sylius\Component\Resource\Model\SoftDeletableInterface;
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderInterface extends
    AdjustableInterface,
    CommentAwareInterface,
    ResourceInterface,
    TimestampableInterface,
    SoftDeletableInterface,
    SequenceSubjectInterface
{
    const STATE_CART        = 'cart';
    const STATE_CART_LOCKED = 'cart_locked';
    const STATE_PENDING     = 'pending';
    const STATE_CONFIRMED   = 'confirmed';
    const STATE_SHIPPED     = 'shipped';
    const STATE_ABANDONED   = 'abandoned';
    const STATE_CANCELLED   = 'cancelled';
    const STATE_RETURNED    = 'returned';

    /**
     * @return Boolean
     */
    public function isCompleted();

    public function complete();

    /**
     * @return \DateTime
     */
    public function getCompletedAt();

    /**
     * @param null|\DateTime $completedAt
     */
    public function setCompletedAt(\DateTime $completedAt = null);

    /**
     * @return Collection|OrderItemInterface[] An array or collection of OrderItemInterface
     */
    public function getItems();

    /**
     * @param Collection|OrderItemInterface[] $items
     */
    public function setItems(Collection $items);

    /**
     * @return integer
     */
    public function countItems();

    /**
     * @param OrderItemInterface $item
     */
    public function addItem(OrderItemInterface $item);

    /**
     * Remove item from order.
     *
     * @param OrderItemInterface $item
     */
    public function removeItem(OrderItemInterface $item);

    /**
     * @param OrderItemInterface $item
     *
     * @return Boolean
     */
    public function hasItem(OrderItemInterface $item);

    /**
     * @return integer
     */
    public function getItemsTotal();

    /**
     * Calculate items total based on the items
     * unit prices and quantities.
     */
    public function calculateItemsTotal();

    /**
     * @return integer
     */
    public function getTotal();

    /**
     * @param integer $total
     */
    public function setTotal($total);

    /**
     * Calculate total.
     * Items total + Adjustments total.
     */
    public function calculateTotal();

    /**
     * Alias of {@link countItems()}.
     *
     * @deprecated To be removed in 1.0. Use {@link countItems()} instead.
     */
    public function getTotalItems();

    /**
     * @return integer
     */
    public function getTotalQuantity();

    /**
     * @return Boolean
     */
    public function isEmpty();

    /**
     * Clears all items in cart.
     */
    public function clearItems();

    /**
     * @return string
     */
    public function getState();

    /**
     * @param string $state
     */
    public function setState($state);

    /**
     * Add an identity to this order. Eg. external identity to refer to an ebay order id.
     *
     * @param IdentityInterface $identity
     */
    public function addIdentity(IdentityInterface $identity);

    /**
     * @param IdentityInterface $identity
     */
    public function removeIdentity(IdentityInterface $identity);

    /**
     * @param IdentityInterface $identity
     */
    public function hasIdentity(IdentityInterface $identity);

    /**
     * @return Collection|IdentityInterface[]
     */
    public function getIdentities();
}
