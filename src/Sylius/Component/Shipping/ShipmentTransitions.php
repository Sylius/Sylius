<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Shipping;

/**
 * @author Arkadiusz Krakowiak <arkadiusz.krakowiak@lakion.com>
 */
final class ShipmentTransitions
{
    const GRAPH = 'sylius_shipment';

    const TRANSITION_CREATE = 'create';
    const TRANSITION_SHIP = 'ship';
    const TRANSITION_CANCEL = 'cancel';

    private function __construct()
    {
    }
}
