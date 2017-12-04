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

final class ShipmentTransitions
{
    public const GRAPH = 'sylius_shipment';

    public const TRANSITION_CREATE = 'create';
    public const TRANSITION_SHIP = 'ship';
    public const TRANSITION_CANCEL = 'cancel';

    private function __construct()
    {
    }
}
