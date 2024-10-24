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

namespace Sylius\Component\Core\Payment;

use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

final class IdBasedInvoiceNumberGenerator implements InvoiceNumberGeneratorInterface
{
    public function generate(OrderInterface $order, PaymentInterface $payment): string
    {
        return $order->getId() . '-' . $payment->getId();
    }
}
