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

final class OrderShippingStates
{
    public const STATE_CART = 'cart';

    public const STATE_READY = 'ready';

    public const STATE_CANCELLED = 'cancelled';

    public const STATE_PARTIALLY_SHIPPED = 'partially_shipped';

    public const STATE_SHIPPED = 'shipped';

    private function __construct()
    {
    }
}
