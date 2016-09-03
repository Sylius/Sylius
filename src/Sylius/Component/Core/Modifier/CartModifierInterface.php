<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Modifier;

use Sylius\Component\Cart\Model\CartInterface;
use Sylius\Component\Cart\Model\CartItemInterface;

/**
 * @author Łukasz Chrusciel <lukasz.chrusciel@lakion.com>
 */
interface CartModifierInterface
{
    /**
     * @param CartInterface $cart
     * @param CartItemInterface $cartItem
     */
    public function addToCart(CartInterface $cart, CartItemInterface $cartItem);

    /**
     * @param CartInterface $cart
     * @param CartItemInterface $item
     */
    public function removeFromCart(CartInterface $cart, CartItemInterface $item);
}
