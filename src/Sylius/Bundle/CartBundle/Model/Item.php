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
    /**
     * Id.
     *
     * @var mixed
     */
    protected $id;

    /**
     * Cart.
     *
     * @var CartInterface
     */
    protected $cart;

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
     * {@inheritdoc}
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * {@inheritdoc}
     */
    public function getQuantity()
    {
        return $this->quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function setQuantity($quantity)
    {
        $this->quantity = $quantity;
    }

    /**
     * {@inheritdoc}
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * {@inheritdoc}
     */
    public function setCart(CartInterface $cart = null)
    {
        $this->cart = $cart;
    }
}
