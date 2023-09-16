<?php

namespace Sylius\Bundle\CoreBundle\StateMachine;

use SM\Factory\FactoryInterface;

final class WinzouStateMachineAdapter implements StateMachineInterface
{
    public function __construct (private FactoryInterface $winzouStateMachineFactory)
    {
    }

    public function can(object $subject, string $graphName, string $transition): bool
    {
        return $this->getStateMachine($subject, $graphName)->can($transition);
    }

    public function apply(object $subject, string $graphName, string $transition, array $context = []): void
    {
        $this->getStateMachine($subject, $graphName)->apply($transition);
    }

    public function getEnabledTransition(object $subject, string $graphName): array
    {
        $transitions = $this->getStateMachine($subject, $graphName)->getPossibleTransitions();

        return array_map(
            fn (string $transition) => new Transition($transition, null, null),
            $transitions
        );
    }

    private function getStateMachine(object $subject, string $graphName): \SM\StateMachine\StateMachineInterface
    {
        return $this->winzouStateMachineFactory->get($subject, $graphName);
    }
}
