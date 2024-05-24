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

namespace spec\Sylius\Bundle\ApiBundle\Applicator;

use PhpSpec\ObjectBehavior;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Exception\StateMachineTransitionFailedException;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class PaymentStateMachineTransitionApplicatorSpec extends ObjectBehavior
{
    function let(StateMachineInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_completes_payment(
        StateMachineInterface $stateMachine,
        PaymentInterface $payment,
    ): void {
        $this->beConstructedWith($stateMachine);

        $stateMachine->can($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE)->willReturn(true);
        $stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE)->shouldBeCalled();

        $this->complete($payment);
    }

    function it_throws_exception_if_cannot_complete_payment(
        StateMachineInterface $stateMachine,
        PaymentInterface $payment,
    ): void {
        $this->beConstructedWith($stateMachine);

        $stateMachine->can($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE)->willReturn(false);
        $stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE)->shouldNotBeCalled();

        $this
            ->shouldThrow(StateMachineTransitionFailedException::class)
            ->during('complete', [$payment])
        ;
    }
}
