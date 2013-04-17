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
 * Interface for cart item model.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CartItemInterface
{
    /**
     * Returns associated cart.
     *
     * @return CartInterface
     */
    public function getCart();

    /**
     * Sets cart.
     *
     * @param CartInterface|null $cart
     */
    public function setCart(CartInterface $cart = null);

    /**
     * Returns quantity.
     *
     * @return integer
     */
    public function getQuantity();

    /**
     * Sets quantity.
     *
     * @param integer $quantity
     */
    public function setQuantity($quantity);

    /**
     * Get item price.
     *
     * @return integer
     */
    public function getUnitPrice();

    /**
     * Set item price.
     *
     * @param float $price
     */
    public function setUnitPrice($price);

    /**
     * Set total.
     *
     * @return integer
     */
    public function getTotal();

    /**
     * Set total.
     *
     * @param integer $total
     */
    public function setTotal($total);

    /**
     * Calculate line total.
     */
    public function calculateTotal();

    /**
     * Checks whether the item given as argument corresponds to
     * the same cart item. Can be overwritten to sum up quantity.
     *
     * @param CartItemInterface $cartItem
     *
     * @return Boolean
     */
    public function equals(CartItemInterface $cartItem);
}
