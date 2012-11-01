<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\Operator;

use Sylius\Bundle\CartBundle\Model\CartInterface;
use Sylius\Bundle\CartBundle\Model\CartItemInterface;

/**
 * Interface for cart operator.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
interface CartOperatorInterface
{
    /**
     * Adds item to cart.
     *
     * @param CartInterface     $cart
     * @param CartItemInterface $item
     */
    public function addItem(CartInterface $cart, CartItemInterface $item);

    /**
     * Removes item from cart.
     *
     * @param CartInterface     $cart
     * @param CartItemInterface $item
     */
    public function removeItem(CartInterface $cart, CartItemInterface $item);

    /**
     * Refreshes cart data.
     *
     * @param CartInterface $cart
     */
    public function refresh(CartInterface $cart);

    /**
     * Saves cart at current state.
     *
     * @param CartInterface $cart
     */
    public function save(CartInterface $cart);

    /**
     * Clears current cart.
     *
     * @param CartInterface $cart
     */
    public function clear(CartInterface $cart);
}
