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

namespace spec\Sylius\Bundle\CoreBundle\StateMachine;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\SMException;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Bundle\CoreBundle\StateMachine\Transition;

final class WinzouStateMachineAdapterSpec extends ObjectBehavior
{
    function let(FactoryInterface $winzouStateMachineFactory): void
    {
        $this->beConstructedWith($winzouStateMachineFactory);
    }

    function it_returns_whether_a_transition_can_be_applied(
        FactoryInterface $winzouStateMachineFactory,
        StateMachineInterface $stateMachine,
        \stdClass $subject,
    ): void {
        $winzouStateMachineFactory->get($subject, 'some_graph')->willReturn($stateMachine);
        $stateMachine->can('transition')->shouldBeCalled()->willReturn(true);

        $this->can($subject, 'some_graph', 'transition')->shouldReturn(true);
    }

    function it_translates_winzou_state_machines_exceptions_to_state_machine_execution_exception_on_the_can_method_call(
        FactoryInterface $winzouStateMachineFactory,
        StateMachineInterface $stateMachine,
        \stdClass $subject,
    ): void {
        $winzouStateMachineFactory->get($subject, 'some_graph')->willReturn($stateMachine);
        $stateMachine->can('transition')->willThrow(new SMException('Invalid argument'));

        $this->shouldThrow(StateMachineExecutionException::class)->during('can', [$subject, 'some_graph', 'transition']);
    }

    function it_applies_a_transition(
        FactoryInterface $winzouStateMachineFactory,
        StateMachineInterface $stateMachine,
        \stdClass $subject,
    ): void {
        $winzouStateMachineFactory->get($subject, 'some_graph')->willReturn($stateMachine);
        $stateMachine->apply('transition')->shouldBeCalled()->willReturn(true);

        $this->apply($subject, 'some_graph', 'transition');
    }

    function it_translates_winzou_state_machines_exceptions_to_state_machine_execution_exception_on_the_apply_method_call(
        FactoryInterface $winzouStateMachineFactory,
        StateMachineInterface $stateMachine,
        \stdClass $subject,
    ): void {
        $winzouStateMachineFactory->get($subject, 'some_graph')->willReturn($stateMachine);
        $stateMachine->apply('transition')->willThrow(new SMException('Invalid argument'));

        $this->shouldThrow(StateMachineExecutionException::class)->during('apply', [$subject, 'some_graph', 'transition']);
    }

    function it_returns_enabled_transitions(
        FactoryInterface $winzouStateMachineFactory,
        StateMachineInterface $stateMachine,
        \stdClass $subject,
    ): void {
        $winzouStateMachineFactory->get($subject, 'some_graph')->willReturn($stateMachine);
        $stateMachine->getPossibleTransitions()->shouldBeCalled()->willReturn(['transition', 'transition2']);

        $this->getEnabledTransitions($subject, 'some_graph')->shouldBeLike([
            new Transition('transition', null, null),
            new Transition('transition2', null, null),
        ]);
    }

    function it_translates_winzou_state_machines_exceptions_to_state_machine_execution_exception_on_the_get_enabled_transition_method_call(
        FactoryInterface $winzouStateMachineFactory,
        StateMachineInterface $stateMachine,
        \stdClass $subject,
    ): void {
        $winzouStateMachineFactory->get($subject, 'some_graph')->willReturn($stateMachine);
        $stateMachine->getPossibleTransitions()->willThrow(new SMException('Invalid argument'));

        $this->shouldThrow(StateMachineExecutionException::class)->during('getEnabledTransitions', [$subject, 'some_graph']);
    }
}
