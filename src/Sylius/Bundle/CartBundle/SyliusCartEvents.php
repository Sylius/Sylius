<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle;

final class SyliusCartEvents
{
    const CART_CHANGE            = 'sylius.cart_change';
    const CART_INITIALIZE        = 'sylius.cart.initialize';
    const CART_ABANDON           = 'sylius.cart.abandon';

    const CART_CLEAR_INITIALIZE  = 'sylius.cart_clear.initialize';
    const CART_CLEAR_COMPLETED   = 'sylius.cart_clear.completed';

    const CART_SAVE_INITIALIZE   = 'sylius.cart_save.initialize';
    const CART_SAVE_COMPLETED    = 'sylius.cart_save.completed';

    const ITEM_ADD_INITIALIZE    = 'sylius.cart_item.add.initialize';
    const ITEM_ADD_COMPLETED     = 'sylius.cart_item.add.completed';
    const ITEM_ADD_ERROR         = 'sylius.cart_item.add.error';

    const ITEM_REMOVE_INITIALIZE = 'sylius.cart_item.remove.initialize';
    const ITEM_REMOVE_COMPLETED  = 'sylius.cart_item.remove.completed';
    const ITEM_REMOVE_ERROR      = 'sylius.cart_item.remove.error';
}
