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

namespace Sylius\Bundle\ApiBundle\Changer;

use Sylius\Bundle\ApiBundle\Exception\PaymentMethodCannotBeChangedException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Repository\PaymentRepositoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Webmozart\Assert\Assert;

final class PaymentMethodChanger implements PaymentMethodChangerInterface
{
    public function __construct(
        private PaymentRepositoryInterface $paymentRepository,
        private PaymentMethodRepositoryInterface $paymentMethodRepository,
    ) {
    }

    public function changePaymentMethod(
        string $paymentMethodCode,
        string $paymentId,
        OrderInterface $order,
    ): OrderInterface {
        /** @var PaymentMethodInterface|null $paymentMethod */
        $paymentMethod = $this->paymentMethodRepository->findOneBy([
            'code' => $paymentMethodCode,
        ]);
        Assert::notNull($paymentMethod, 'Payment method has not been found');

        $payment = $this->paymentRepository->findOneByOrderId($paymentId, $order->getId());
        Assert::notNull($payment, 'Can not find payment with given identifier.');

        if ($order->getState() === OrderInterface::STATE_NEW) {
            Assert::same(
                $payment->getState(),
                PaymentInterface::STATE_NEW,
                'Can not change payment method for this payment',
            );
            $payment->setMethod($paymentMethod);

            return $order;
        }

        throw new PaymentMethodCannotBeChangedException();
    }
}
