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

namespace Sylius\Component\Core\StateMachine;

final class CompositeStateMachine implements StateMachineInterface
{
    /** @var array<StateMachineInterface> */
    private array $stateMachineAdapters;

    public function __construct (
        iterable $stateMachineAdapters,
    ) {
        $this->stateMachineAdapters = $stateMachineAdapters instanceof \Traversable ? iterator_to_array($stateMachineAdapters) : $stateMachineAdapters;
    }

    public function can(object $subject, string $graphName, string $transition): bool
    {
        $lastException = null;

        foreach ($this->stateMachineAdapters as $stateMachineAdapter) {
            try {
                return $stateMachineAdapter->can($subject, $graphName, $transition);
            } catch (\Exception $exception) {
                printf("Could not apply transition %s on subject %s by %s state machine\n", $transition, get_class($subject), get_class($stateMachineAdapter));

                $lastException = $exception;
                continue;
            }
        }

        throw $lastException;
    }

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        $lastException = null;

        foreach ($this->stateMachineAdapters as $stateMachine) {
            try {
                $stateMachine->apply($subject, $graphName, $transition, $context);
                return;
            } catch (\Exception $exception) {
                printf("Could not apply transition %s on subject %s by %s state machine\n", $transition, get_class($subject), get_class($stateMachine));
                $lastException = $exception;
                continue;
            }
        }

        throw $lastException;
    }

    public function getEnabledTransitions(object $subject, string $graphName): array
    {
        $lastException = null;

        foreach ($this->stateMachineAdapters as $stateMachine) {
            try {
                return $stateMachine->getEnabledTransitions($subject, $graphName);
            } catch (\Exception $exception) {
                $lastException = $exception;
                continue;
            }
        }

        throw $lastException;
    }
}
