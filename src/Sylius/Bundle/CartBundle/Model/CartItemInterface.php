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
     * @param CartInterface
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
     * @param $quantity
     */
    public function setQuantity($quantity);

    /**
     * Get item price.
     */
    public function getUnitPrice();

    /**
     * Set item price.
     */
    public function setUnitPrice($price);

    /**
     * Set total.
     */
    public function getTotal();

    /**
     * Set total.
     */
    public function setTotal($total);

    /**
     * Calulcate line total.
     */
    public function calculateTotal();

    /**
     * Checks whether the item given as argument corresponds to
     * the same cart item. Can be overriden to sum up quantity.
     *
     * @param CartItemInterface $cartItem
     *
     * @return Boolean
     */
    public function equals(CartItemInterface $cartItem);
}
