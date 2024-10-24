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

interface StateMachineInterface
{
    /**
     * @throws StateMachineExecutionException
     */
    public function can(object $subject, string $graphName, string $transition): bool;

    /**
     * @param array<string, mixed> $context
     *
     * @throws StateMachineExecutionException
     */
    public function apply(object $subject, string $graphName, string $transition, array $context = []): void;

    /**
     * @throws StateMachineExecutionException
     *
     * @return array<TransitionInterface>
     */
    public function getEnabledTransitions(object $subject, string $graphName): array;

    /**
     * @throws StateMachineExecutionException
     */
    public function getTransitionFromState(object $subject, string $graphName, string $fromState): ?string;

    /**
     * @throws StateMachineExecutionException
     */
    public function getTransitionToState(object $subject, string $graphName, string $toState): ?string;
}
