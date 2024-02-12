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

use Traversable;
use Webmozart\Assert\Assert;

class CompositeStateMachine implements StateMachineInterface
{
    /** @var array<StateMachineInterface> */
    private array $stateMachineAdapters;

    /**
     * @param iterable<StateMachineInterface> $stateMachineAdapters
     * @param array<string, string> $graphsToAdaptersMapping
     */
    public function __construct(
        iterable $stateMachineAdapters,
        private string $defaultAdapter,
        private array $graphsToAdaptersMapping,
    ) {
        $validStateMachineAdapters = array_filter(
            $stateMachineAdapters,
            fn (mixed $stateMachineAdapter) => $stateMachineAdapter instanceof StateMachineInterface,
        );

        Assert::notEmpty($validStateMachineAdapters, 'At least one state machine adapter should be provided.');
        $this->stateMachineAdapters = $validStateMachineAdapters;
    }

    /**
     * @throws \Exception
     */
    public function can(object $subject, string $graphName, string $transition): bool
    {
        return $this->getStateMachineAdapter($graphName)->can($subject, $graphName, $transition);
    }

    /**
     * @throws \Exception
     */
    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        $this->getStateMachineAdapter($graphName)->apply($subject, $graphName, $transition, $context);
    }

    /**
     * @throws \Exception
     */
    public function getEnabledTransitions(object $subject, string $graphName): array
    {
        return $this->getStateMachineAdapter($graphName)->getEnabledTransitions($subject, $graphName);
    }

    public function getTransitionFromState(object $subject, string $graphName, string $fromState): ?string
    {
        return $this->getStateMachineAdapter($graphName)->getTransitionFromState($subject, $graphName, $fromState);
    }

    public function getTransitionToState(object $subject, string $graphName, string $toState): ?string
    {
        return $this->getStateMachineAdapter($graphName)->getTransitionToState($subject, $graphName, $toState);
    }

    private function getStateMachineAdapter(string $graphName): StateMachineInterface
    {
        if (isset($this->graphsToAdaptersMapping[$graphName])) {
            return $this->stateMachineAdapters[$this->graphsToAdaptersMapping[$graphName]];
        }

        return $this->stateMachineAdapters[$this->defaultAdapter];
    }
}
