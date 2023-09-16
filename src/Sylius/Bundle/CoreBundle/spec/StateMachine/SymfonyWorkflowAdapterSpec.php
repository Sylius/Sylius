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
use Sylius\Bundle\CoreBundle\StateMachine\Transition;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition as SymfonyWorkflowTransition;
use Symfony\Component\Workflow\Workflow;

final class SymfonyWorkflowAdapterSpec extends ObjectBehavior
{
    function let(Registry $symfonyWorkflowRegistry): void
    {
        $this->beConstructedWith($symfonyWorkflowRegistry);
    }

    function it_returns_whether_a_transition_can_be_applied(
        Registry $symfonyWorkflowRegistry,
        Workflow $someWorkflow,
        \stdClass $subject,
    ): void {
        $symfonyWorkflowRegistry->get($subject, 'some_workflow')->willReturn($someWorkflow);
        $someWorkflow->can($subject, 'transition')->shouldBeCalled()->willReturn(true);

        $this->can($subject, 'some_workflow', 'transition')->shouldReturn(true);
    }

    function it_applies_a_transition(
        Registry $symfonyWorkflowRegistry,
        Workflow $someWorkflow,
        \stdClass $subject,
    ): void {
        $symfonyWorkflowRegistry->get($subject, 'some_workflow')->willReturn($someWorkflow);
        $someWorkflow->apply($subject, 'transition', [])->shouldBeCalled();

        $this->apply($subject, 'some_workflow', 'transition');
    }

    function it_returns_enabled_transitions(
        Registry $symfonyWorkflowRegistry,
        Workflow $someWorkflow,
        \stdClass $subject,
    ): void {
        $symfonyWorkflowRegistry->get($subject, 'some_workflow')->willReturn($someWorkflow);
        $someWorkflow->getEnabledTransitions($subject)->shouldBeCalled()->willReturn([
            new SymfonyWorkflowTransition('transition', 'from', 'to'),
            new SymfonyWorkflowTransition('transition2', ['from'], ['to']),
        ]);

        $this->getEnabledTransition($subject, 'some_workflow')->shouldBeLike([
            new Transition('transition', ['from'], ['to']),
            new Transition('transition2', ['from'], ['to']),
        ]);
    }
}
