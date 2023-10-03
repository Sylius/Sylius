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
use Sylius\Bundle\CoreBundle\StateMachine\StateMachineInterface;

final class CompositeStateMachineSpec extends ObjectBehavior
{
    function it_invokes_the_can_method_on_a_default_state_machine_adapter_when_no_state_machine_is_mapped_to_a_given_graph(
        StateMachineInterface $winzouStateMachineAdapter,
        StateMachineInterface $symfonyWorkflowAdapter,
    ): void {
        $this->beConstructedWith(
            [
                'winzou_state_machine' => $winzouStateMachineAdapter,
                'symfony_workflow' => $symfonyWorkflowAdapter,
            ],
            'winzou_state_machine',
            [],
        );

        $subject = new \stdClass();

        $winzouStateMachineAdapter->can($subject, 'my_graph', 'my_transition')->willReturn(true);
        $symfonyWorkflowAdapter->can($subject, 'my_graph', 'my_transition')->shouldNotBeCalled();

        $this->can($subject, 'my_graph', 'my_transition')->shouldReturn(true);
    }

    function it_invokes_the_can_method_on_a_state_machine_assigned_to_a_given_graph(
        StateMachineInterface $winzouStateMachineAdapter,
        StateMachineInterface $symfonyWorkflowAdapter,
    ): void {
        $this->beConstructedWith(
            [
                'winzou_state_machine' => $winzouStateMachineAdapter,
                'symfony_workflow' => $symfonyWorkflowAdapter,
            ],
            'winzou_state_machine',
            [
                'my_graph' => 'symfony_workflow',
            ],
        );

        $subject = new \stdClass();

        $winzouStateMachineAdapter->can($subject, 'my_graph', 'my_transition')->shouldNotBeCalled();
        $symfonyWorkflowAdapter->can($subject, 'my_graph', 'my_transition')->willReturn(true);

        $this->can($subject, 'my_graph', 'my_transition')->shouldReturn(true);
    }

    function it_invokes_the_apply_method_on_a_default_state_machine_adapter_when_no_state_machine_is_mapped_to_a_given_graph(
        StateMachineInterface $winzouStateMachineAdapter,
        StateMachineInterface $symfonyWorkflowAdapter,
    ): void {
        $this->beConstructedWith(
            [
                'winzou_state_machine' => $winzouStateMachineAdapter,
                'symfony_workflow' => $symfonyWorkflowAdapter,
            ],
            'winzou_state_machine',
            [],
        );

        $subject = new \stdClass();

        $winzouStateMachineAdapter->apply($subject, 'my_graph', 'my_transition', [])->shouldBeCalled();
        $symfonyWorkflowAdapter->apply($subject, 'my_graph', 'my_transition', [])->shouldNotBeCalled();

        $this->apply($subject, 'my_graph', 'my_transition');
    }

    function it_invokes_the_apply_method_on_a_state_machine_assigned_to_a_given_graph(
        StateMachineInterface $winzouStateMachineAdapter,
        StateMachineInterface $symfonyWorkflowAdapter,
    ): void {
        $this->beConstructedWith(
            [
                'winzou_state_machine' => $winzouStateMachineAdapter,
                'symfony_workflow' => $symfonyWorkflowAdapter,
            ],
            'winzou_state_machine',
            [
                'my_graph' => 'symfony_workflow',
            ],
        );

        $subject = new \stdClass();

        $winzouStateMachineAdapter->apply($subject, 'my_graph', 'my_transition', [])->shouldNotBeCalled();
        $symfonyWorkflowAdapter->apply($subject, 'my_graph', 'my_transition', [])->shouldBeCalled();

        $this->apply($subject, 'my_graph', 'my_transition');
    }

    function it_invokes_the_get_enabled_transitions_method_on_a_default_state_machine_adapter_when_no_state_machine_is_mapped_to_a_given_graph(
        StateMachineInterface $winzouStateMachineAdapter,
        StateMachineInterface $symfonyWorkflowAdapter,
    ): void {
        $this->beConstructedWith(
            [
                'winzou_state_machine' => $winzouStateMachineAdapter,
                'symfony_workflow' => $symfonyWorkflowAdapter,
            ],
            'winzou_state_machine',
            [],
        );

        $subject = new \stdClass();

        $winzouStateMachineAdapter->getEnabledTransitions($subject, 'my_graph')->shouldBeCalled()->willReturn([]);
        $symfonyWorkflowAdapter->getEnabledTransitions($subject, 'my_graph')->shouldNotBeCalled();

        $this->getEnabledTransitions($subject, 'my_graph')->shouldReturn([]);
    }

    function it_invokes_the_get_enabled_transitions_method_on_a_state_machine_assigned_to_a_given_graph(
        StateMachineInterface $winzouStateMachineAdapter,
        StateMachineInterface $symfonyWorkflowAdapter,
    ): void {
        $this->beConstructedWith(
            [
                'winzou_state_machine' => $winzouStateMachineAdapter,
                'symfony_workflow' => $symfonyWorkflowAdapter,
            ],
            'winzou_state_machine',
            [
                'my_graph' => 'symfony_workflow',
            ],
        );

        $subject = new \stdClass();

        $winzouStateMachineAdapter->getEnabledTransitions($subject, 'my_graph')->shouldNotBeCalled();
        $symfonyWorkflowAdapter->getEnabledTransitions($subject, 'my_graph')->shouldBeCalled()->willReturn([]);

        $this->getEnabledTransitions($subject, 'my_graph')->shouldReturn([]);
    }
}
