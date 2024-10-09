<?php

declare(strict_types=1);

namespace Sylius\Bundle\PaymentBundle\Checker;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;

final class PaymentRequestFinalTransitionChecker implements PaymentRequestFinalTransitionCheckerInterface
{
    public function __construct(
        private StateMachineInterface $stateMachine,
    ) {
    }

    public function isFinal(PaymentRequestInterface $paymentRequest): bool {
        $state = $paymentRequest->getState();
        $nextTransition = $this->stateMachine->getTransitionFromState(
            $paymentRequest,
            PaymentRequestTransitions::GRAPH,
            $state
        );

        return null === $nextTransition;
    }
}
