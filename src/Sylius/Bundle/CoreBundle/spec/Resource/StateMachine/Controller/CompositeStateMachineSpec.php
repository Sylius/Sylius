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

namespace spec\Sylius\Bundle\CoreBundle\Resource\StateMachine\Controller;

use PhpSpec\ObjectBehavior;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Component\Resource\Model\ResourceInterface;

final class CompositeStateMachineSpec extends ObjectBehavior
{
    function let(StateMachineInterface $stateMachine): void
    {
        $this->beConstructedWith($stateMachine);
    }

    function it_returns_true_if_can_transition(
        StateMachineInterface $stateMachine,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource,
    ): void
    {
        $requestConfiguration->hasStateMachine()->willReturn(true);
        $requestConfiguration->getStateMachineGraph()->willReturn('default');
        $requestConfiguration->getStateMachineTransition()->willReturn('transition');

        $stateMachine->can($resource, 'default', 'transition')->willReturn(true);

        $this->can($requestConfiguration, $resource)->shouldReturn(true);
    }

    function it_applies_transition(
        StateMachineInterface $stateMachine,
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource,
    ): void
    {
        $requestConfiguration->hasStateMachine()->willReturn(true);
        $requestConfiguration->getStateMachineGraph()->willReturn('default');
        $requestConfiguration->getStateMachineTransition()->willReturn('transition');

        $stateMachine->apply($resource, 'default', 'transition')->shouldBeCalled();

        $this->apply($requestConfiguration, $resource);
    }
}
