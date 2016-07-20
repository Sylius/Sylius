<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShippingStates
{
    const CART = 'cart';
    const READY = 'ready';
    const CANCELLED = 'cancelled';
    const PARTIALLY_SHIPPED = 'partially_shipped';
    const SHIPPED = 'shipped';
}
