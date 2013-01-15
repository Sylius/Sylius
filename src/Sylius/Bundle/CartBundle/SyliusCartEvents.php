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
    const CART_CLEAR_INITIALIZE  = 'sylius_cart.clear.initialize';
    const CART_CLEAR_COMPLETED   = 'sylius_cart.clear.completed';

    const CART_SAVE_INITIALIZE   = 'sylius_cart.save.initialize';
    const CART_SAVE_COMPLETED    = 'sylius_cart.save.completed';

    const ITEM_ADD_INITIALIZE    = 'sylius_cart.item.add.initialize';
    const ITEM_ADD_COMPLETED     = 'sylius_cart.item.add.completed';
    const ITEM_ADD_ERROR         = 'sylius_cart.item.add.error';

    const ITEM_REMOVE_INITIALIZE = 'sylius_cart.item.remove.initialize';
    const ITEM_REMOVE_COMPLETED  = 'sylius_cart.item.remove.completed';
    const ITEM_REMOVE_ERROR      = 'sylius_cart.item.remove.error';
}
