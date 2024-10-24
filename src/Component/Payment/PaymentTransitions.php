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

interface PaymentTransitions
{
    public const GRAPH = 'sylius_payment';

    public const TRANSITION_CREATE = 'create';

    public const TRANSITION_PROCESS = 'process';

    public const TRANSITION_COMPLETE = 'complete';

    public const TRANSITION_FAIL = 'fail';

    public const TRANSITION_CANCEL = 'cancel';

    public const TRANSITION_AUTHORIZE = 'authorize';

    public const TRANSITION_REFUND = 'refund';

    public const TRANSITION_VOID = 'void';
}
