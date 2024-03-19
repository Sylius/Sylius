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

namespace Sylius\Abstraction\StateMachine;

use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Symfony\Component\Workflow\Exception\ExceptionInterface as WorkflowExceptionInterface;
use Symfony\Component\Workflow\Registry;
use Symfony\Component\Workflow\Transition as SymfonyWorkflowTransition;

final class SymfonyWorkflowAdapter implements StateMachineInterface
{
    public function __construct(private Registry $symfonyWorkflowRegistry)
    {
    }

    public function can(object $subject, string $graphName, string $transition): bool
    {
        try {
            return $this->symfonyWorkflowRegistry->get($subject, $graphName)->can($subject, $transition);
        } catch (WorkflowExceptionInterface $exception) {
            throw new StateMachineExecutionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        try {
            $this->symfonyWorkflowRegistry->get($subject, $graphName)->apply($subject, $transition, $context);
        } catch (WorkflowExceptionInterface $exception) {
            throw new StateMachineExecutionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function getEnabledTransitions(object $subject, string $graphName): array
    {
        try {
            $enabledTransitions = $this->symfonyWorkflowRegistry->get($subject, $graphName)->getEnabledTransitions($subject);
        } catch (WorkflowExceptionInterface $exception) {
            throw new StateMachineExecutionException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return array_map(
            function (SymfonyWorkflowTransition $transition): TransitionInterface {
                return new Transition(
                    $transition->getName(),
                    $transition->getFroms(),
                    $transition->getTos(),
                );
            },
            $enabledTransitions,
        );
    }

    public function getTransitionFromState(object $subject, string $graphName, string $fromState): ?string
    {
        foreach ($this->getEnabledTransitions($subject, $graphName) as $transition) {
            if ($transition->getFroms() !== null && in_array($fromState, $transition->getFroms(), true)) {
                return $transition->getName();
            }
        }

        return null;
    }

    public function getTransitionToState(object $subject, string $graphName, string $toState): ?string
    {
        foreach ($this->getEnabledTransitions($subject, $graphName) as $transition) {
            if ($transition->getTos() !== null && in_array($toState, $transition->getTos(), true)) {
                return $transition->getName();
            }
        }

        return null;
    }
}
