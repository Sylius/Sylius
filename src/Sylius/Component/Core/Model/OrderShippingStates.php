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
    const BACKORDER = 'backorder';
    const CANCELLED = 'cancelled';
    const CHECKOUT = 'checkout';
    const ONHOLD = 'onhold';
    const PARTIALLY_SHIPPED = 'partially_shipped';
    const READY = 'ready';
    const RETURNED = 'returned';
    const SHIPPED = 'shipped';
}
