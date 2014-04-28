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

class InventoryUnitTransitions
{
    const GRAPH = 'sylius_inventory_unit';

    const SYLIUS_HOLD       = 'hold';
    const SYLIUS_BACKORDER  = 'backorder';
    const SYLIUS_SOLD       = 'sold';
    const SYLIUS_RETURN     = 'return';
}
