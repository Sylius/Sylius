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
 * Default order shipping states.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class OrderShippingStates
{
    const CHECKOUT = 'checkout';
    const ONHOLD = 'onhold';
    const READY = 'ready';
    const BACKORDER = 'backorder';
    const PARTIALLY_SHIPPED = 'partially_shipped';
    const SHIPPED = 'shipped';
    const RETURNED = 'returned';
    const CANCELLED = 'cancelled';
}
