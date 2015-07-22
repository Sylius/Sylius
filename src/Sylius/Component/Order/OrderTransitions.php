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

class OrderTransitions
{
    const GRAPH = 'sylius_order';

    const SYLIUS_CREATE = 'create';
    const SYLIUS_RELEASE = 'release';
    const SYLIUS_CONFIRM = 'confirm';
    const SYLIUS_SHIP = 'ship';
    const SYLIUS_ABANDON = 'abandon';
    const SYLIUS_CANCEL = 'cancel';
    const SYLIUS_RETURN = 'return';
}
