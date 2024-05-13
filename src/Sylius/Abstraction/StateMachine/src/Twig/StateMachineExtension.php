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

namespace Sylius\Abstraction\StateMachine\Twig;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

final class StateMachineExtension extends AbstractExtension
{
    public function __construct(private readonly StateMachineInterface $stateMachine)
    {
    }

    public function getFunctions(): array
    {
        return [
            new TwigFunction('sylius_sm_can', $this->stateMachine->can(...)),
            new TwigFunction('sylius_sm_transitions', $this->stateMachine->getEnabledTransitions(...)),
        ];
    }
}
