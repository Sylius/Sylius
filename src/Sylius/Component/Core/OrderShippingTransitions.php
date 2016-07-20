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

use Sylius\Component\Shipping\ShipmentTransitions;

class OrderShippingTransitions extends ShipmentTransitions
{
    const GRAPH = 'sylius_order_shipping';

    const TRANSITION_REQUEST_SHIPPING = 'request_shipping';
    const TRANSITION_PARTIALLY_SHIP = 'partially_ship';
}
