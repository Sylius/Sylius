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

class SyliusShipmentEvents
{
    const PRE_SHIP  = 'sylius.shipment.pre_ship';
    const POST_SHIP = 'sylius.shipment.post_ship';
}
