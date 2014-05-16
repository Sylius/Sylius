<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core;

use Sylius\Component\Payment\PaymentTransitions;

class OrderPaymentTransitions extends PaymentTransitions
{
    const GRAPH = 'sylius_order_payment';
}
