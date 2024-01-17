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
        $stateMachine->apply(PaymentTransitions::TRANSITION_COMPLETE)->shouldBeCalled();

        $this->complete($payment);
    }

    function it_uses_the_new_state_machine_abstraction_if_passed(
        StateMachineInterface $stateMachine,
        PaymentInterface $payment,
    ): void {
        $this->beConstructedWith($stateMachine);

        $stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_COMPLETE)->shouldBeCalled();

        $this->complete($payment);
    }
}
