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

use SM\Factory\FactoryInterface;
use SM\SMException;
use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Component\Resource\StateMachine\StateMachineInterface as ResourceStateMachineInterface;

final class WinzouStateMachineAdapter implements StateMachineInterface
{
    public function __construct(private FactoryInterface $winzouStateMachineFactory)
    {
    }

    public function can(object $subject, string $graphName, string $transition): bool
    {
        try {
            return $this->getStateMachine($subject, $graphName)->can($transition);
        } catch (SMException $exception) {
            throw new StateMachineExecutionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        try {
            $this->getStateMachine($subject, $graphName)->apply($transition);
        } catch (SMException $exception) {
            throw new StateMachineExecutionException($exception->getMessage(), $exception->getCode(), $exception);
        }
    }

    public function getEnabledTransitions(object $subject, string $graphName): array
    {
        try {
            $transitions = $this->getStateMachine($subject, $graphName)->getPossibleTransitions();
        } catch (SMException $exception) {
            throw new StateMachineExecutionException($exception->getMessage(), $exception->getCode(), $exception);
        }

        return array_map(
            fn (string $transition) => new Transition($transition, null, null),
            $transitions,
        );
    }

    public function getTransitionFromState(object $subject, string $graphName, string $fromState): ?string
    {
        $stateMachine = $this->getStateMachine($subject, $graphName);

        if (!$stateMachine instanceof ResourceStateMachineInterface) {
            throw new StateMachineExecutionException(sprintf(
                "Method %s::%s() is not supported when the state machine is not an instance of %s.",
                self::class,
                __FUNCTION__,
                ResourceStateMachineInterface::class,
            ));
        }

        return $stateMachine->getTransitionFromState($fromState);
    }

    public function getTransitionToState(object $subject, string $graphName, string $toState): ?string
    {
        $stateMachine = $this->getStateMachine($subject, $graphName);

        if (!$stateMachine instanceof ResourceStateMachineInterface) {
            throw new StateMachineExecutionException(sprintf(
                "Method %s::%s() is not supported when the state machine is not an instance of %s.",
                self::class,
                __FUNCTION__,
                ResourceStateMachineInterface::class,
            ));
        }

        return $stateMachine->getTransitionToState($toState);
    }

    private function getStateMachine(object $subject, string $graphName): \SM\StateMachine\StateMachineInterface
    {
        return $this->winzouStateMachineFactory->get($subject, $graphName);
    }
}
