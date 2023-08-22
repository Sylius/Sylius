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

use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface as WinzouStateMachineInterface;

final class WinzouStateMachineAdapter implements StateMachineInterface
{
    public function __construct (
        private FactoryInterface $stateMachineFactory,
    ) {
    }

    public function can(object $subject, string $graphName, string $transition): bool
    {
        $stateMachine = $this->getStateMachine($subject, $graphName);

        return $stateMachine->can($transition);
    }

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        $stateMachine = $this->getStateMachine($subject, $graphName);

        $stateMachine->apply($transition);
    }

    public function getEnabledTransitions(object $subject, string $graphName): array
    {
        $stateMachine = $this->getStateMachine($subject, $graphName);

        return $stateMachine->getPossibleTransitions();
    }

    private function getStateMachine(object $subject, string $graphName): WinzouStateMachineInterface
    {
        return $this->stateMachineFactory->get($subject, $graphName);
    }
}
