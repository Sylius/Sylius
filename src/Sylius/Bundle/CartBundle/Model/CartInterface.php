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
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CartInterface
{
    /**
     * Returns id.
     * 
     * @return integer
     */
    function getId();

    /**
     * Returns total item count.
     * 
     * @return integer
     */
    function getTotalItems();
    
    /**
     * Sets total item count.
     * 
     * @param integer $totalItems
     */
    function setTotalItems($totalItems);
    
    function isLocked();
    function setLocked($locked);
    
    /**
     * Checks whether the cart is empty.
     */
    function isEmpty();
    
    function setItems($items);
    
    /**
     * Returns all items from cart.
     * 
     * @return array
     */
    function getItems();

    /**
     * Returns number of items in cart.
     * 
     * @return integer
     */
    function countItems();

    /**
     * Adds item to cart.
     * 
     * @param ItemInterface $item
     */
    function addItem(ItemInterface $item);

    /**
     * Remove item from cart.
     * 
     * @param ItemInterface $item
     */
    function removeItem(ItemInterface $item);
    
    /**
     * Has item in cart?
     * 
     * @param Item
     */
    function hasItem(ItemInterface $item);
    
    /**
     * Removes all items from cart.
     * 
     * @return null
     */
    function clearItems();
    
    /**
     * Returns expiration time.
     * 
     * @return \DateTime
     */
    function getExpiresAt();
    
    /**
     * Sets expiration time.
     * 
     * @param \DateTime $expiresAt
     */
    function setExpiresAt(\DateTime $expiresAt);
    
    /**
     * Increments expiration time.
     * 
     * @return null
     */
    function incrementExpiresAt();
}