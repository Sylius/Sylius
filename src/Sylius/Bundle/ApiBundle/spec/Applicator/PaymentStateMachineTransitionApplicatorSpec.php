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
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachine as WinzouStateMachine;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Exception\StateMachineTransitionFailedException;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class PaymentStateMachineTransitionApplicatorSpec extends ObjectBehavior
{
    function let(StateMachineFactoryInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_completes_payment(
        StateMachineFactoryInterface $stateMachineFactory,
        PaymentInterface $payment,
        WinzouStateMachine $stateMachine,
    ): void {
        $stateMachineFactory->get($payment, PaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(PaymentTransitions::TRANSITION_COMPLETE)->willReturn(true);
        $stateMachine->apply(PaymentTransitions::TRANSITION_COMPLETE)->shouldBeCalled();

        $this->complete($payment);
    }

    function it_throws_exception_if_cannot_complete_payment(
        StateMachineFactoryInterface $stateMachineFactory,
        PaymentInterface $payment,
        WinzouStateMachine $stateMachine,
    ): void {
        $stateMachineFactory->get($payment, PaymentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(PaymentTransitions::TRANSITION_COMPLETE)->willReturn(false);
        $stateMachine->apply(PaymentTransitions::TRANSITION_COMPLETE)->shouldNotBeCalled();

        $this
            ->shouldThrow(StateMachineTransitionFailedException::class)
            ->during('complete', [$payment])
        ;
    }

    function it_uses_the_new_state_machine_abstraction_if_passed(
        StateMachineInterface $stateMachine,
        PaymentInterface $payment,
    ): void {
        $this->beConstructedWith($stateMachine);

        $stateMachine->can($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE)->willReturn(true);
        $stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE)->shouldBeCalled();

        $this->complete($payment);
    }

    function it_throws_exception_if_cannot_complete_payment_with_new_state_machine_abstraction(
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
