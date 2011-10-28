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

/**
 * Extended order interface.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ExtendedOrderInterface extends OrderInterface
{
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
    
    function getItems();
    
    function setItems(array $items);

    /**
     * Adds item to order.
     * 
     * @param ItemInterface $item
     */
    function addItem(ItemInterface $item);

    /**
     * Remove item from order.
     * 
     * @param ItemInterface $item
     */
    function removeItem(ItemInterface $item);
    
    /**
     * Has item in order?
     * 
     * @param Item
     */
    function hasItem(ItemInterface $item);
    
    /**
     * Returns number of order items.
     * 
     * @return integer
     */
    function countItems();
    
    /**
     * Removes all items from order.
     * 
     * @return null
     */
    function clearItems();
}
