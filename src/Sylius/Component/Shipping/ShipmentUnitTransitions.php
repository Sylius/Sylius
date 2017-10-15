<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Shipping;

class ShipmentUnitTransitions
{
    public const GRAPH = 'sylius_shipment_unit';

    public const SYLIUS_CREATE = 'create';
    public const SYLIUS_SHIP = 'ship';
    public const SYLIUS_CANCEL = 'cancel';
}
