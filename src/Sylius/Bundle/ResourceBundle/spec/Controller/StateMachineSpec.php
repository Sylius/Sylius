<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ResourceBundle\Controller;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ResourceBundle\Controller\RequestConfiguration;
use Sylius\Bundle\ResourceBundle\Controller\StateMachineInterface as ResourceStateMachineInterface;
use Sylius\Component\Resource\Model\ResourceInterface;

final class StateMachineSpec extends ObjectBehavior
{
    public function let(FactoryInterface $stateMachineFactory): void
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    public function it_implements_state_machine_interface(): void
    {
        $this->shouldImplement(ResourceStateMachineInterface::class);
    }

    public function it_throws_an_exception_if_transition_is_not_defined_during_can(RequestConfiguration $requestConfiguration, ResourceInterface $resource): void
    {
        $requestConfiguration->hasStateMachine()->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('State machine must be configured to apply transition, check your routing.'))
            ->during('can', [$requestConfiguration, $resource])
        ;
    }

    public function it_throws_an_exception_if_transition_is_not_defined_during_apply(RequestConfiguration $requestConfiguration, ResourceInterface $resource): void
    {
        $requestConfiguration->hasStateMachine()->willReturn(false);

        $this
            ->shouldThrow(new \InvalidArgumentException('State machine must be configured to apply transition, check your routing.'))
            ->during('apply', [$requestConfiguration, $resource])
        ;
    }

    public function it_returns_if_configured_state_machine_can_transition(
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ): void {
        $requestConfiguration->hasStateMachine()->willReturn(true);
        $requestConfiguration->getStateMachineGraph()->willReturn('sylius_product_review_state');
        $requestConfiguration->getStateMachineTransition()->willReturn('reject');

        $stateMachineFactory->get($resource, 'sylius_product_review_state')->willReturn($stateMachine);
        $stateMachine->can('reject')->willReturn(true);

        $this->can($requestConfiguration, $resource)->shouldReturn(true);
    }

    public function it_applies_configured_state_machine_transition(
        RequestConfiguration $requestConfiguration,
        ResourceInterface $resource,
        FactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ): void {
        $requestConfiguration->hasStateMachine()->willReturn(true);
        $requestConfiguration->getStateMachineGraph()->willReturn('sylius_product_review_state');
        $requestConfiguration->getStateMachineTransition()->willReturn('reject');

        $stateMachineFactory->get($resource, 'sylius_product_review_state')->willReturn($stateMachine);
        $stateMachine->apply('reject')->shouldBeCalled();

        $this->apply($requestConfiguration, $resource);
    }
}
