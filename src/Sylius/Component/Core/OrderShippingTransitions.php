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

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class OrderShippingTransitions
{
    const GRAPH = 'sylius_order_shipping';

    const TRANSITION_REQUEST_SHIPPING = 'request_shipping';
    const TRANSITION_PARTIALLY_SHIP = 'partially_ship';
    const TRANSITION_SHIP = 'ship';
    const TRANSITION_CANCEL = 'cancel';

    private function __construct()
    {
    }
}
