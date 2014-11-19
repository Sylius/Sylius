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

class OrderCheckoutTransitions
{
    const GRAPH = 'sylius_order_checkout';

    const SYLIUS_ADDRESSING = 'addressing';
    const SYLIUS_PAYMENT    = 'payment';
    const SYLIUS_SHIPPING   = 'shipping';
    const SYLIUS_FINALIZE   = 'finalize';
    const SYLIUS_COMPLETE   = 'complete';
}
