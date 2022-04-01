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

namespace Sylius\Component\Core\OrderCheckout;

interface AsynchronousOrderCheckoutTransitions
{
    public const GRAPH = 'sylius_headless_order_checkout';

    public const TRANSITION_PROCESS = 'process';

    public const TRANSITION_COMPLETE = 'complete';
}
