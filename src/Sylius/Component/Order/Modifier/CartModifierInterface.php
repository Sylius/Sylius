<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order\Modifier;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;

/**
 * @author Łukasz Chrusciel <lukasz.chrusciel@lakion.com>
 */
interface CartModifierInterface
{
    /**
     * @param OrderInterface $cart
     * @param OrderItemInterface $cartItem
     */
    public function addToCart(OrderInterface $cart, OrderItemInterface $cartItem);

    /**
     * @param OrderInterface $cart
     * @param OrderItemInterface $item
     */
    public function removeFromCart(OrderInterface $cart, OrderItemInterface $item);
}
