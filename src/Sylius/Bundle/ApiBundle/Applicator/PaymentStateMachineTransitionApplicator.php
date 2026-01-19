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

namespace Sylius\Bundle\ApiBundle\Applicator;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Exception\StateMachineTransitionFailedException;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

final readonly class PaymentStateMachineTransitionApplicator implements PaymentStateMachineTransitionApplicatorInterface
{
    public function __construct(private StateMachineInterface $stateMachine)
    {
    }

    public function complete(PaymentInterface $data): PaymentInterface
    {
        $this->applyTransition($data, PaymentTransitions::TRANSITION_COMPLETE);

        return $data;
    }

    public function refund(PaymentInterface $data): PaymentInterface
    {
        $this->applyTransition($data, PaymentTransitions::TRANSITION_REFUND);

        return $data;
    }

    private function applyTransition(PaymentInterface $payment, string $transition): void
    {
        if (false === $this->stateMachine->can($payment, PaymentTransitions::GRAPH, $transition)) {
            throw new StateMachineTransitionFailedException(sprintf(
                'Transition "%s" cannot be applied on "%s" payment.',
                $transition,
                $payment->getState(),
            ));
        }

        $this->stateMachine->apply($payment, PaymentTransitions::GRAPH, $transition);
    }
}
