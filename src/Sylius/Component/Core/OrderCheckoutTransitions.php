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

namespace Sylius\Component\Core;

final class OrderCheckoutTransitions
{
    public const GRAPH = 'sylius_order_checkout';

    public const TRANSITION_ADDRESS = 'address';
    public const TRANSITION_COMPLETE = 'complete';
    public const TRANSITION_SELECT_PAYMENT = 'select_payment';
    public const TRANSITION_SELECT_SHIPPING = 'select_shipping';
    public const TRANSITION_SKIP_PAYMENT = 'skip_payment';
    public const TRANSITION_SKIP_SHIPPING = 'skip_shipping';

    private function __construct()
    {
    }
}
