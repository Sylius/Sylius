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

namespace Sylius\Component\Payment\Canceller;

use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

/** @experimental */
final class PaymentRequestCanceller implements PaymentRequestCancellerInterface
{
    /**
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(private PaymentRequestRepositoryInterface $paymentRequestRepository)
    {
    }

    public function cancelPaymentRequests(int|string $paymentId, string $paymentMethodCode): void
    {
        $paymentRequests = $this->paymentRequestRepository->findByStatesAndPaymentId([PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING], $paymentId);

        if ($paymentRequests === []) {
            return;
        }

        foreach ($paymentRequests as $paymentRequest) {
            if ($paymentRequest->getMethod()->getCode() !== $paymentMethodCode) {
                $paymentRequest->setState(PaymentRequestInterface::STATE_CANCELLED);
            }
        }
    }
}
