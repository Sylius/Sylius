<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Order;

class OrderPaymentTransitions
{
    const GRAPH = 'sylius_order_payment';

    const SYLIUS_CREATE   = 'create';
    const SYLIUS_PROCESS  = 'process';
    const SYLIUS_COMPLETE = 'complete';
    const SYLIUS_FAIL     = 'fail';
    const SYLIUS_CANCEL   = 'cancel';
    const SYLIUS_REFUND   = 'refund';
}
