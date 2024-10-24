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

namespace Sylius\Component\Core;

interface OrderCheckoutStates
{
    public const STATE_ADDRESSED = 'addressed';

    public const STATE_CART = 'cart';

    public const STATE_COMPLETED = 'completed';

    public const STATE_PAYMENT_SELECTED = 'payment_selected';

    public const STATE_PAYMENT_SKIPPED = 'payment_skipped';

    public const STATE_SHIPPING_SELECTED = 'shipping_selected';

    public const STATE_SHIPPING_SKIPPED = 'shipping_skipped';
}
