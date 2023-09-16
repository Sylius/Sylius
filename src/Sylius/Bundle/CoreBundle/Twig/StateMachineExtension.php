<?php

namespace Sylius\Bundle\CoreBundle\Twig;

use Sylius\Bundle\CoreBundle\StateMachine\StateMachineInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class StateMachineExtension extends AbstractExtension
{
    public function __construct(private StateMachineInterface $stateMachine)
    {
    }

    /**
     * @return array<TwigFunction>
     */
    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_state_machine_can', [$this->stateMachine, 'can']),
            new TwigFunction('sylius_state_machine_get_enabled_transitions', [$this->stateMachine, 'getEnabledTransitions']),
        ];
    }
}
