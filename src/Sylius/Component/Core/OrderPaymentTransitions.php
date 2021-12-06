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

interface OrderPaymentTransitions
{
    public const GRAPH = 'sylius_order_payment';

    public const TRANSITION_REQUEST_PAYMENT = 'request_payment';

    public const TRANSITION_PARTIALLY_AUTHORIZE = 'partially_authorize';

    public const TRANSITION_AUTHORIZE = 'authorize';

    public const TRANSITION_PARTIALLY_PAY = 'partially_pay';

    public const TRANSITION_CANCEL = 'cancel';

    public const TRANSITION_PAY = 'pay';

    public const TRANSITION_PARTIALLY_REFUND = 'partially_refund';

    public const TRANSITION_REFUND = 'refund';
}
