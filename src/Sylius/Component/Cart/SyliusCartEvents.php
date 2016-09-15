<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart;

final class SyliusCartEvents
{
    const CART_CHANGE = 'sylius.cart_change';
    const CART_INITIALIZE = 'sylius.cart.initialize';
    const CART_ABANDON = 'sylius.cart.abandon';

    private function __construct()
    {
    }
}
