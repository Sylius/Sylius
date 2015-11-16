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

final class CartEvents
{
    const CHANGE     = 'sylius.cart.change';
    const INITIALIZE = 'sylius.cart.initialize';
    const ABANDON    = 'sylius.cart.abandon';

    const PRE_SAVE   = 'sylius.cart.pre_save';
    const POST_SAVE  = 'sylius.cart.post_save';
    const PRE_CLEAR  = 'sylius.cart.pre_clear';
    const POST_CLEAR = 'sylius.cart.post_clear';
}
