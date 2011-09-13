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
 * Model for cart items.
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
abstract class Item implements ItemInterface
{
    protected $id;
    
    /**
     * Cart.
     * 
     * @var CartInterface
     */
    protected $cart;
    
    protected $quantity;
    
    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->quantity = 0;
    }

    public function getId()
    {
        return $this->id;
    }
    
    public function getQuantity()
    {
        return $this->quantity;
    }
    
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }
    
    public function getCart()
    {
        return $this->cart;
    }
    
    /**
     * Sets cart.
     * 
     * @param CartInterface $cart
     */
    public function setCart(CartInterface $cart)
    {
        $this->cart = $cart;
    }
}
