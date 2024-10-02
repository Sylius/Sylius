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

namespace Sylius\Bundle\PaymentBundle\Canceller;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Payment\Canceller\PaymentRequestCancellerInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

final class PaymentRequestCanceller implements PaymentRequestCancellerInterface
{
    /**
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     */
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private StateMachineInterface $stateMachine,
    ) {
    }

    public function cancelPaymentRequests(mixed $paymentId, string $paymentMethodCode): void
    {
        $paymentRequests = $this->paymentRequestRepository->findByPaymentIdAndStates($paymentId, [PaymentRequestInterface::STATE_NEW, PaymentRequestInterface::STATE_PROCESSING]);

        foreach ($paymentRequests as $paymentRequest) {
            if ($paymentRequest->getMethod()->getCode() !== $paymentMethodCode) {
                $this->stateMachine->apply(
                    $paymentRequest,
                    PaymentRequestTransitions::GRAPH,
                    PaymentRequestTransitions::TRANSITION_CANCEL,
                );
            }
        }
    }
}
