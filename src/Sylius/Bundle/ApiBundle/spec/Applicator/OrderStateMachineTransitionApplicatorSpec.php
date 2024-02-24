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

namespace spec\Sylius\Bundle\ApiBundle\Applicator;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachine as WinzouStateMachine;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Exception\StateMachineTransitionFailedException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

final class OrderStateMachineTransitionApplicatorSpec extends ObjectBehavior
{
    function let(StateMachineFactoryInterface $stateMachineFactory)
    {
        $this->beConstructedWith($stateMachineFactory);
    }

    function it_cancels_order(
        StateMachineFactoryInterface $stateMachineFactory,
        OrderInterface $order,
        WinzouStateMachine $stateMachine,
    ): void {
        $stateMachineFactory->get($order, OrderTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderTransitions::TRANSITION_CANCEL)->willReturn(true);
        $stateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $this->cancel($order);
    }

    function it_throw_exception_if_cannot_cancel_order(
        StateMachineFactoryInterface $stateMachineFactory,
        OrderInterface $order,
        WinzouStateMachine $stateMachine,
    ): void {
        $stateMachineFactory->get($order, OrderTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderTransitions::TRANSITION_CANCEL)->willReturn(false);
        $stateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldNotBeCalled();

        $this
            ->shouldThrow(StateMachineTransitionFailedException::class)
            ->during('cancel', [$order])
        ;
    }

    function it_uses_the_new_state_machine_abstraction_if_passed(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $this->beConstructedWith($stateMachine);
        $stateMachine->can($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->willReturn(true);
        $stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $this->cancel($order);
    }

    function it_throw_exception_if_cannot_cancel_order_with_new_state_machine_abstraction(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $this->beConstructedWith($stateMachine);
        $stateMachine->can($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->willReturn(false);
        $stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->shouldNotBeCalled();

        $this
            ->shouldThrow(StateMachineTransitionFailedException::class)
            ->during('cancel', [$order])
        ;
    }
}
