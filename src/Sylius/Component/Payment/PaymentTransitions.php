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

class PaymentTransitions
{
    const GRAPH = 'sylius_payment';

    const SYLIUS_AUTHORIZED = 'authorized';
    const SYLIUS_CANCEL     = 'cancel';
    const SYLIUS_COMPLETE   = 'complete';
    const SYLIUS_CREATE     = 'create';
    const SYLIUS_FAIL       = 'fail';
    const SYLIUS_PROCESS    = 'process';
    const SYLIUS_REFUND     = 'refund';
    const SYLIUS_VOID       = 'void';
}
