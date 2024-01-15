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
use Symfony\Component\Workflow\Exception\InvalidArgumentException;
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
        } catch (InvalidArgumentException $exception) {
            throw new StateMachineExecutionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        try {
            $this->symfonyWorkflowRegistry->get($subject, $graphName)->apply($subject, $transition, $context);
        } catch (InvalidArgumentException $exception) {
            throw new StateMachineExecutionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function getEnabledTransitions(object $subject, string $graphName): array
    {
        try {
            $enabledTransitions = $this->symfonyWorkflowRegistry->get($subject, $graphName)->getEnabledTransitions($subject);
        } catch (InvalidArgumentException $exception) {
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
}
