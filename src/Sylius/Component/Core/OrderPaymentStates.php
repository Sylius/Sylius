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

interface OrderPaymentStates
{
    public const STATE_CART = 'cart';

    public const STATE_AWAITING_PAYMENT = 'awaiting_payment';

    public const STATE_PARTIALLY_AUTHORIZED = 'partially_authorized';

    public const STATE_AUTHORIZED = 'authorized';

    public const STATE_PARTIALLY_PAID = 'partially_paid';

    public const STATE_CANCELLED = 'cancelled';

    public const STATE_PAID = 'paid';

    public const STATE_PARTIALLY_REFUNDED = 'partially_refunded';

    public const STATE_REFUNDED = 'refunded';
}
