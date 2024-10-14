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

use Doctrine\Persistence\ObjectManager;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Payment\Canceller\PaymentRequestCancellerInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;
use Sylius\Component\Payment\Repository\PaymentRequestRepositoryInterface;

/** @experimental */
final class PaymentRequestCanceller implements PaymentRequestCancellerInterface
{
    /**
     * @param PaymentRequestRepositoryInterface<PaymentRequestInterface> $paymentRequestRepository
     * @param array<string> $paymentRequestStatesToBeCancelled
     */
    public function __construct(
        private PaymentRequestRepositoryInterface $paymentRequestRepository,
        private StateMachineInterface $stateMachine,
        private ObjectManager $objectManager,
        private array $paymentRequestStatesToBeCancelled,
    ) {
    }

    public function cancelPaymentRequests(mixed $paymentId, string $paymentMethodCode): void
    {
        $paymentRequests = $this->paymentRequestRepository->findByPaymentIdAndStates($paymentId, $this->paymentRequestStatesToBeCancelled);

        foreach ($paymentRequests as $paymentRequest) {
            if ($paymentRequest->getMethod()->getCode() !== $paymentMethodCode) {
                $this->stateMachine->apply(
                    $paymentRequest,
                    PaymentRequestTransitions::GRAPH,
                    PaymentRequestTransitions::TRANSITION_CANCEL,
                );

                $this->objectManager->persist($paymentRequest);
            }
        }

        $this->objectManager->flush();
    }
}
