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

namespace Sylius\Bundle\CoreBundle\StateMachine;

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
        Assert::notEmpty($stateMachineAdapters, 'At least one state machine adapter should be provided.');
        Assert::allIsInstanceOf(
            $stateMachineAdapters,
            StateMachineInterface::class,
            sprintf('All state machine adapters should implement the "%s" interface.', StateMachineInterface::class),
        );
        $this->stateMachineAdapters = $stateMachineAdapters instanceof Traversable ? iterator_to_array($stateMachineAdapters) : $stateMachineAdapters;
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

    private function getStateMachineAdapter(string $graphName): StateMachineInterface
    {
        if (isset($this->graphsToAdaptersMapping[$graphName])) {
            return $this->stateMachineAdapters[$this->graphsToAdaptersMapping[$graphName]];
        }

        return $this->stateMachineAdapters[$this->defaultAdapter];
    }
}
