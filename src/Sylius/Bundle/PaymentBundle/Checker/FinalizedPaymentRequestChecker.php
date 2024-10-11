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

namespace Sylius\Bundle\PaymentBundle\Checker;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;

/** @experimental */
final class FinalizedPaymentRequestChecker implements FinalizedPaymentRequestCheckerInterface
{
    public function __construct(
        private StateMachineInterface $stateMachine,
    ) {
    }

    public function isFinal(PaymentRequestInterface $paymentRequest): bool
    {
        $state = $paymentRequest->getState();
        $nextTransition = $this->stateMachine->getTransitionFromState(
            $paymentRequest,
            PaymentRequestTransitions::GRAPH,
            $state,
        );

        return null === $nextTransition;
    }
}
