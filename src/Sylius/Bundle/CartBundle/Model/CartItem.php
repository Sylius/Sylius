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
class CartItem implements CartItemInterface
{
    /**
     * Cart item id.
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
     * Unit price.
     *
     * @var float
     */
    protected $unitPrice;

    /**
     * Total value.
     *
     * @var float
     */
    protected $total;

    /**
     * Constructor.
     */
    public function __construct()
    {
        $this->quantity = 1;
        $this->unitPrice = 0;
        $this->total = 0;
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

        return $this;
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

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getUnitPrice()
    {
        return $this->unitPrice;
    }

    /**
     * {@inheritdoc}
     */
    public function setUnitPrice($unitPrice)
    {
        $this->unitPrice = $unitPrice;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * {@inheritdoc}
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function calculateTotal()
    {
        $this->total = $this->quantity * $this->unitPrice;

        return $this;
    }

    /**
     * {@inheritdoc}
     */
    public function equals(CartItemInterface $cartItem)
    {
        return $this->getId() === $cartItem->getId();
    }
}
