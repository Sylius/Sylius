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

namespace Sylius\Component\Core\Payment\Remover;

use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

final class OrderPaymentsRemover implements OrderPaymentsRemoverInterface
{
    public function canRemovePayments(OrderInterface $order): bool
    {
        return 0 === $order->getTotal();
    }

    public function removePayments(OrderInterface $order): void
    {
        $removablePayments = $order->getPayments()->filter(function (PaymentInterface $payment): bool {
            return $payment->getState() === PaymentInterface::STATE_CART;
        });

        foreach ($removablePayments as $payment) {
            $order->removePayment($payment);
        }
    }
}
