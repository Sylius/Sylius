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
 * Model for order items.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class Item implements ItemInterface
{
    /**
     * Item id.
     * 
     * @var integer
     */
    protected $id;
    
    /**
     * Order.
     * 
     * @var OrderInterface
     */
    protected $order;
    
    /**
     * Quantity.
     * 
     * @var integer
     */
    protected $quantity;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->quantity = 0;
    }
    
    /**
     * Returns item id.
     * 
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }
    
    /**
     * Get item quantity.
     * 
     * @return integer
     */
    public function getQuantity()
    {
        return $this->quantity;
    }
    
    /**
     * Sets quantity.
     * 
     * @param integer $quantity
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    
    /**
     * Returns order.
     * 
     * @return OrderInterface
     */
    public function getOrder()
    {
        return $this->order;
    }
    
    /**
     * Sets order.
     * 
     * @param OrderInterface $order
     */
    public function setOrder(OrderInterface $order)
    {
        $this->order = $order;
    }
}
