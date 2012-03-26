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
use Sylius\Bundle\CartBundle\Model\ItemInterface;

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
     * @param CartInterface $cart
     * @param ItemInterface	$item
     */
    function addItem(CartInterface $cart, ItemInterface $item);

    /**
     * Removes item from cart.
     *
     * @param CartInterface $cart
     * @param ItemInterface $item
     */
    function removeItem(CartInterface $cart, ItemInterface $item);

    /**
     * Refreshes cart data.
     *
     * @param CartInterface $cart
     */
    function refresh(CartInterface $cart);

    /**
     * Validates cart.
     *
     * @param CartInterface $cart
     *
     * @return Boolean
     */
    function validate(CartInterface $cart);

    /**
     * Saves cart at current state.
     *
     * @param CartInterface $cart
     */
    function save(CartInterface $cart);

    /**
     * Clears current cart.
     *
     * @param CartInterface $cart
     */
    function clear(CartInterface $cart);
}
