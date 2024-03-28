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

namespace Sylius\Bundle\CoreBundle\Resource\StateMachine\Controller;

use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\StateMachineInterface as ResourceStateMachineInterface;
use Sylius\Component\Resource\Model\ResourceInterface;
use Webmozart\Assert\Assert;

final class CompositeStateMachine implements ResourceStateMachineInterface
{
    public function __construct(
        private readonly StateMachineInterface $compositeStateMachine,
    ) {
    }

    public function can(RequestConfiguration $configuration, ResourceInterface $resource): bool
    {
        Assert::true($configuration->hasStateMachine(), 'State machine must be configured to apply transition, check your routing.');

        $graph = $configuration->getStateMachineGraph();

        /** @var string $transitionName */
        $transitionName = $configuration->getStateMachineTransition();

        return $this->compositeStateMachine->can($resource, $graph, $transitionName);
    }

    public function apply(RequestConfiguration $configuration, ResourceInterface $resource): void
    {
        Assert::true($configuration->hasStateMachine(), 'State machine must be configured to apply transition, check your routing.');

        $graph = $configuration->getStateMachineGraph();

        /** @var string $transitionName */
        $transitionName = $configuration->getStateMachineTransition();

        $this->compositeStateMachine->apply($resource, $graph, $transitionName);
    }
}
