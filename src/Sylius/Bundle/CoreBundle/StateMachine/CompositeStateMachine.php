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

use Sylius\Bundle\CoreBundle\StateMachine\Exception\StateMachineExecutionException;
use Traversable;
use Webmozart\Assert\Assert;

class CompositeStateMachine implements StateMachineInterface
{
    /** @var array<StateMachineInterface> */
    private array $stateMachineAdapters;

    /**
     * @param iterable<StateMachineInterface> $stateMachineAdapters
     */
    public function __construct(iterable $stateMachineAdapters)
    {
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
        $lastException = new \Exception();

        foreach ($this->stateMachineAdapters as $stateMachineAdapter) {
            try {
                return $stateMachineAdapter->can($subject, $graphName, $transition);
            } catch (StateMachineExecutionException $exception) {
                $lastException = $exception;
            }
        }

        throw $lastException;
    }

    /**
     * @throws \Exception
     */
    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        $lastException = new \Exception();

        foreach ($this->stateMachineAdapters as $stateMachineAdapter) {
            try {
                $stateMachineAdapter->apply($subject, $graphName, $transition, $context);

                return;
            } catch (StateMachineExecutionException $exception) {
                $lastException = $exception;
            }
        }

        throw $lastException;
    }

    /**
     * @throws \Exception
     */
    public function getEnabledTransitions(object $subject, string $graphName): array
    {
        $lastException = new \Exception();

        foreach ($this->stateMachineAdapters as $stateMachineAdapter) {
            try {
                return $stateMachineAdapter->getEnabledTransitions($subject, $graphName);
            } catch (StateMachineExecutionException $exception) {
                $lastException = $exception;
            }
        }

        throw $lastException;
    }
}
