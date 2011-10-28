<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\SalesBundle\Manipulator;

use Sylius\Bundle\SalesBundle\Model\OrderInterface;

/**
 * Order manipulator interface.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface OrderManipulatorInterface
{
    /**
     * Places an order.
     * 
     * @param OrderInterface $order
     */
    function place(OrderInterface $order);
    
    /**
     * Creates an order.
     * 
     * @param OrderInterface $order
     */
    function create(OrderInterface $order);
    
    /**
     * Updates an order.
     * 
     * @param OrderInterface $order
     */
    function update(OrderInterface $order);
    
    /**
     * Deletes an order.
     * 
     * @param OrderInterface $order
     */
    function delete(OrderInterface $order);
    
    /**
     * Confirms an order.
     * 
     * @param OrderInterface $order
     */
    function confirm(OrderInterface $order);
    
    /**
     * Saves order status.
     * 
     * @param OrderInterface $order
     */
    function status(OrderInterface $order);
    
    /**
     * Closes an order.
     * 
     * @param OrderInterface $order
     */
    function close(OrderInterface $order);
    
    /**
     * Opens an order.
     * 
     * @param OrderInterface $order
     */
    function open(OrderInterface $order);
}
