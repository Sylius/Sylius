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

namespace Sylius\Component\Payment;

final class PaymentTransitions
{
    public const GRAPH = 'sylius_payment';

    public const TRANSITION_CREATE = 'create';
    public const TRANSITION_PROCESS = 'process';
    public const TRANSITION_COMPLETE = 'complete';
    public const TRANSITION_FAIL = 'fail';
    public const TRANSITION_CANCEL = 'cancel';
    public const TRANSITION_REFUND = 'refund';
    public const TRANSITION_VOID = 'void';

    private function __construct()
    {
    }
}
