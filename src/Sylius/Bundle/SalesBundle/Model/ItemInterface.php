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
 * Interface for order item model.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface ItemInterface
{
	/**
     * Returns item id.
     * 
     * @return integer
     */
    function getId();

    /**
     * Returns order.
     * 
     * @return OrderInterface
     */
    function getOrder();
    
    /**
     * Sets order.
     * 
     * @param OrderInterface $order
     */
    function setOrder(OrderInterface $order);
    
    /**
     * Get item quantity.
     * 
     * @return integer
     */
    function getQuantity();
    
    /**
     * Sets quantity.
     * 
     * @param integer $quantity
     */
    function setQuantity($quantity);
}
