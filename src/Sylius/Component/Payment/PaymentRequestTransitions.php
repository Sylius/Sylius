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

namespace Sylius\Component\Payment;

/** @experimental */
interface PaymentRequestTransitions
{
    public const GRAPH = 'sylius_payment_request';

    public const TRANSITION_COMPLETE = 'complete';

    public const TRANSITION_CANCEL = 'cancel';

    public const TRANSITION_FAIL = 'fail';

    public const TRANSITION_PROCESS = 'process';
}
