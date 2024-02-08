<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Component\Shipping;

@trigger_error('This class is deprecated since Sylius 1.12 and will be removed in 2.0. Copy these constants if you use them.', \E_USER_DEPRECATED);

class ShipmentUnitTransitions
{
    public const GRAPH = 'sylius_shipment_unit';

    public const SYLIUS_CREATE = 'create';

    public const SYLIUS_SHIP = 'ship';

    public const SYLIUS_CANCEL = 'cancel';
}
