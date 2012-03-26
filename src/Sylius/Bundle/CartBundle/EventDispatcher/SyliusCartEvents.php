<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\CartBundle\EventDispatcher;

/**
 * Events.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
final class SyliusCartsEvents
{
    const ITEM_ADD    = 'sylius_cart.event.item.add';
    const ITEM_REMOVE = 'sylius_cart.event.item.remove';
    const CART_UPDATE = 'sylius_cart.event.cart.update';
    const CART_CLEAR  = 'sylius_cart.event.cart.clear';
    const CART_FLUSH  = 'sylius_cart.event.cart.flush';
}
