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

/**
 * Cart model interface.
 * All driver cart entities or documents should implement this interface.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CartInterface
{
    /**
     * Returns cart id.
     *
     * @return mixed
     */
    function getId();

    /**
     * Sets cart id.
     *
     * @param mixed $id
     */
    function setId($id);

    /**
     * Returns number of items in cart.
     *
     * @return integer
     */
    function getTotalItems();

    /**
     * Sets number of items in cart.
     *
     * @param integer $totalItems;
     */
    function setTotalItems($totalItems);

    /**
     * Increments total items number by given amount.
     * Default is 1.
     *
     * @param integer $amount
     */
    function incrementTotalItems($amount = 1);

    /**
     * Checks whether the cart is locked or not.
     * If cart is left unlocked, it should be deleted after expiration time.
     *
     * @return Boolean
     */
    function isLocked();

    /**
     * Sets whether the cart is locked or not.
     *
     * @param Boolean $locked
     */
    function setLocked($locked);

    /**
     * Checks whether the cart is empty or not.
     *
     * @return Boolean
     */
    function isEmpty();

    /**
     * Counts manually all items in cart.
     *
     * @return integer
     */
    function countItems();

    /**
     * Clears all items in cart.
     */
    function clearItems();

    /**
     * Sets collection of items
     *
     * @param mixed $items
     */
    function setItems($items);

    /**
     * Returns collection of items in cart.
     *
     * @return mixed
     */
    function getItems();

    /**
     * Adds item to cart.
     *
     * @param ItemInterface $item
     */
    function addItem(ItemInterface $item);

    /**
     * Removes item from cart.
     *
     * @param ItemInterface $item
     */
    function removeItem(ItemInterface $item);

    /**
     * Checks whether given item is inside cart or not.
     *
     * @return Boolean
     */
    function hasItem(ItemInterface $item);

    /**
     * Gets expiration time.
     *
     * @return \DateTime
     */
    function getExpiresAt();

    /**
     * Sets expiration time.
     *
     * @param \DateTime $expiresAt
     */
    function setExpiresAt(\DateTime $expiresAt = null);

    /**
     * Bumps the expiration time.
     * Default is +3 hours.
     */
    function incrementExpiresAt();

    /**
     * Checks whether the cart is expired or not.
     *
     * @return Boolean
     */
    function isExpired();
}
