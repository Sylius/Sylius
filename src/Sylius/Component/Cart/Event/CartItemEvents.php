<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Cart\Event;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class CartItemEvents
{
    const PRE_ADD       = 'sylius.cart_item.pre_add';
    const POST_ADD      = 'sylius.cart_item.post_add';
    const ADD_FAILED    = 'sylius.cart_item.add_failed';
    const PRE_REMOVE    = 'sylius.cart_item.pre_remove';
    const POST_REMOVE   = 'sylius.cart_item.post_remove';
    const REMOVE_FAILED = 'sylius.cart_item.remove_failed';
}
