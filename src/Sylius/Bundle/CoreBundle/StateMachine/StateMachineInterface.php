<?php

namespace Sylius\Bundle\CoreBundle\StateMachine;

interface StateMachineInterface
{
    public function can(object $subject, string $graphName, string $transition): bool;

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void;

    /**
     * @return array<TransitionInterface>
     */
    public function getEnabledTransition(object $subject, string $graphName): array;
}
