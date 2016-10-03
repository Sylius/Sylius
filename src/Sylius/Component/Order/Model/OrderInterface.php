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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface OrderInterface extends
    AdjustableInterface,
    ResourceInterface,
    TimestampableInterface
{
    const STATE_CART = 'cart';
    const STATE_NEW = 'new';
    const STATE_CANCELLED = 'cancelled';
    const STATE_FULFILLED = 'fulfilled';

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
     * @return string
     */
    public function getNumber();

    /**
     * @param string
     */
    public function setNumber($number);

    /**
     * @return string
     */
    public function getNotes();

    /**
     * @param string $notes
     */
    public function setNotes($notes);

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
