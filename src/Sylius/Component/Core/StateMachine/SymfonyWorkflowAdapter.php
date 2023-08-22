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

use Symfony\Component\Workflow\Registry;

final class SymfonyWorkflowAdapter implements StateMachineInterface
{
    public function __construct (
        private Registry $workflows,
    ) {
    }

    public function can(object $subject, string $graphName, string $transition): bool
    {
        $workflow = $this->getWorkflow($subject, $graphName);

        return $workflow->can($subject, $transition);
    }

    /** @param array<string, mixed> $context */
    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        $workflow = $this->getWorkflow($subject, $graphName);

        $workflow->apply($subject, $transition);
    }

    /** @return array<TransitionInterface> */
    public function getEnabledTransitions(object $subject, string $graphName): array
    {
        $workflow = $this->getWorkflow($subject, $graphName);

        return $workflow->getEnabledTransitions($subject);
    }

    public function getWorkflow(object $subject, string $graphName): object
    {
        return $this->workflows->get($subject, $graphName);
    }
}
