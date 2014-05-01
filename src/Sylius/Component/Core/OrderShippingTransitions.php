<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order;

class OrderShippingTransitions
{
    const GRAPH = 'sylius_order_shipping';

    const SYLIUS_HOLD           = 'hold';
    const SYLIUS_RELEASE        = 'release';
    const SYLIUS_BACKORDER      = 'backorder';
    const SYLIUS_PREPARE        = 'prepare';
    const SYLIUS_SHIP           = 'ship';
    const SYLIUS_PARTIALLY_SHIP = 'partially_ship';
    const SYLIUS_RETURN         = 'return';
    const SYLIUS_CANCEL         = 'cancel';
}
