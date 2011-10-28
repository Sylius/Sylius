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
 * Order manager interface.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderManagerInterface
{  
    /**
     * Creates new order object.
     * 
     * @return OrderInterface
     */
    function createOrder();

    /**
     * Persist order.
     * 
     * @param OrderInterface
     */
    function persistOrder(OrderInterface $order);
    
    /**
     * Removes order.
     * 
     * @param OrderInterface $order
     */
    function removeOrder(OrderInterface $order);
    
    /**
     * Finds order by id.
     * 
     * @param integer $id
     * @return OrderInterface
     */
    function findOrder($id);
    
    /**
     * Finds order by criteria.
     * 
     * @param array $criteria
     * @return OrderInterface
     */
    function findOrderBy(array $criteria);
    
    /**
     * Finds all orders.
     * 
     * @return array
     */
    function findOrders();
    
    /**
     * Finds orders by criteria.
     * 
     * @param array $criteria
     * @return array
     */
    function findOrdersBy(array $criteria);
    
    /**
     * Returns FQCN of order.
     * 
     * @return string
     */
    function getClass();
}
