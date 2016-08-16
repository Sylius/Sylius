<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class OrderShippingStates
{
    const STATE_CART = 'cart';
    const STATE_READY = 'ready';
    const STATE_CANCELLED = 'cancelled';
    const STATE_PARTIALLY_SHIPPED = 'partially_shipped';
    const STATE_SHIPPED = 'shipped';

    private function __construct()
    {
    }
}
