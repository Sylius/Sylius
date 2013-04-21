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

use Doctrine\Common\Collections\Collection;

/**
 * Cart model interface.
 * All driver cart entities or documents should implement this interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CartInterface
{
    /**
     * Returns number of items in cart.
     *
     * @return integer
     */
    public function getTotalItems();

    /**
     * Sets number of items in cart.
     *
     * @param integer $totalItems
     */
    public function setTotalItems($totalItems);

    /**
     * Change total items number by given amount.
     *
     * @param integer $amount
     */
    public function changeTotalItems($amount);

    /**
     * Returns total quantity of items in cart.
     *
     * @return integer
     */
    public function getTotalQuantity();

    /**
     * Sets total quantity of items in cart.
     *
     * @param integer $totalQuantity
     */
    public function setTotalQuantity($totalQuantity);

    /**
     * Change total quantity number by given amount.
     *
     * @param integer $amount
     */
    public function changeTotalQuantity($amount);

    /**
     * Get total cart value.
     */
    public function getTotal();

    /**
     * Set total value.
     */
    public function setTotal($total);

    /**
     * Calculate total.
     */
    public function calculateTotal();

    /**
     * Checks whether the cart is locked or not.
     * If cart is left unlocked, it should be deleted after expiration time.
     *
     * @return Boolean
     */
    public function isLocked();

    /**
     * Sets whether the cart is locked or not.
     *
     * @param Boolean $locked
     */
    public function setLocked($locked);

    /**
     * Checks whether the cart is empty or not.
     *
     * @return Boolean
     */
    public function isEmpty();

    /**
     * Counts manually all items in cart.
     *
     * @return integer
     */
    public function countItems();

    /**
     * Clears all items in cart.
     */
    public function clearItems();

    /**
     * Sets collection of items
     *
     * @param Collection $items
     */
    public function setItems(Collection $items);

    /**
     * Returns collection of items in cart.
     *
     * @return mixed
     */
    public function getItems();

    /**
     * Adds item to cart.
     *
     * @param CartItemInterface $item
     */
    public function addItem(CartItemInterface $item);

    /**
     * Removes item from cart.
     *
     * @param CartItemInterface $item
     */
    public function removeItem(CartItemInterface $item);

    /**
     * Checks whether given item is inside cart or not.
     *
     * @param CartItemInterface $item
     *
     * @return Boolean
     */
    public function hasItem(CartItemInterface $item);

    /**
     * Gets expiration time.
     *
     * @return \DateTime
     */
    public function getExpiresAt();

    /**
     * Sets expiration time.
     *
     * @param \DateTime|null $expiresAt
     */
    public function setExpiresAt(\DateTime $expiresAt = null);

    /**
     * Bumps the expiration time.
     * Default is +3 hours.
     */
    public function incrementExpiresAt();

    /**
     * Checks whether the cart is expired or not.
     *
     * @return Boolean
     */
    public function isExpired();
}
