<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Payment;

/**
 * @author Grzegorz Sadowski <grzegorz.sadowski@lakion.com>
 */
final class PaymentTransitions
{
    const GRAPH = 'sylius_payment';

    const TRANSITION_CREATE = 'create';
    const TRANSITION_PROCESS = 'process';
    const TRANSITION_COMPLETE = 'complete';
    const TRANSITION_FAIL = 'fail';
    const TRANSITION_CANCEL = 'cancel';
    const TRANSITION_REFUND = 'refund';
    const TRANSITION_VOID = 'void';

    private function __construct()
    {
    }
}
