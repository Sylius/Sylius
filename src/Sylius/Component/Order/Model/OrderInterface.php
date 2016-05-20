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
use Sylius\Component\Resource\Model\TimestampableInterface;
use Sylius\Component\Sequence\Model\SequenceSubjectInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderInterface extends
    AdjustableInterface,
    CommentAwareInterface,
    ResourceInterface,
    SequenceSubjectInterface,
    TimestampableInterface
{
    const STATE_CART = 'cart';
    const STATE_CART_LOCKED = 'cart_locked';
    const STATE_PENDING = 'pending';
    const STATE_CONFIRMED = 'confirmed';
    const STATE_SHIPPED = 'shipped';
    const STATE_ABANDONED = 'abandoned';
    const STATE_CANCELLED = 'cancelled';
    const STATE_RETURNED = 'returned';

    /**
     * @return bool
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
     * @return int
     */
    public function countItems();

    /**
     * @param OrderItemInterface $item
     */
    public function addItem(OrderItemInterface $item);

    /**
     * @param OrderItemInterface $item
     */
    public function removeItem(OrderItemInterface $item);

    /**
     * @param OrderItemInterface $item
     *
     * @return bool
     */
    public function hasItem(OrderItemInterface $item);

    /**
     * @return int
     */
    public function getItemsTotal();

    public function recalculateItemsTotal();

    /**
     * @return int
     */
    public function getTotal();

    /**
     * @return int
     */
    public function getTotalQuantity();

    /**
     * @return bool
     */
    public function isEmpty();

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

    /**
     * @return string
     */
    public function getAdditionalInformation();

    /**
     * @param string $information
     */
    public function setAdditionalInformation($information);

    /**
     * @param string|null $type
     *
     * @return array
     */
    public function getAdjustmentsRecursively($type = null);

    /**
     * @param string|null $type
     *
     * @return int
     */
    public function getAdjustmentsTotalRecursively($type = null);

    /**
     * @param string|null $type
     */
    public function removeAdjustmentsRecursively($type = null);
}
