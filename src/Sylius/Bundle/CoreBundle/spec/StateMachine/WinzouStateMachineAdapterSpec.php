<?php

namespace spec\Sylius\Bundle\CoreBundle\StateMachine;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use stdClass;
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
        stdClass $subject,
    ): void {
        $winzouStateMachineFactory->get($subject, 'some_graph')->willReturn($stateMachine);
        $stateMachine->can('transition')->shouldBeCalled()->willReturn(true);

        $this->can($subject, 'some_graph', 'transition')->shouldReturn(true);
    }

    function it_applies_a_transition(
        FactoryInterface $winzouStateMachineFactory,
        StateMachineInterface $stateMachine,
        stdClass $subject,
    ): void {
        $winzouStateMachineFactory->get($subject, 'some_graph')->willReturn($stateMachine);
        $stateMachine->apply('transition')->shouldBeCalled()->willReturn(true);

        $this->apply($subject, 'some_graph', 'transition');
    }

    function it_returns_enabled_transitions(
        FactoryInterface $winzouStateMachineFactory,
        StateMachineInterface $stateMachine,
        stdClass $subject,
    ): void {
        $winzouStateMachineFactory->get($subject, 'some_graph')->willReturn($stateMachine);
        $stateMachine->getPossibleTransitions()->shouldBeCalled()->willReturn(['transition', 'transition2']);

        $this->getEnabledTransition($subject, 'some_graph')->shouldBeLike([
            new Transition('transition', null, null),
            new Transition('transition2', null, null),
        ]);
    }
}
