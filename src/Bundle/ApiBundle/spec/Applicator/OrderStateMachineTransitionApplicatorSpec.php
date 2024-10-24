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
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Exception\StateMachineTransitionFailedException;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

final class OrderStateMachineTransitionApplicatorSpec extends ObjectBehavior
{
    function let(StateMachineInterface $stateMachine)
    {
        $this->beConstructedWith($stateMachine);
    }

    function it_cancels_order(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $stateMachine->can($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->willReturn(true);
        $stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $this->cancel($order);
    }

    function it_throw_exception_if_cannot_cancel_order(
        StateMachineInterface $stateMachine,
        OrderInterface $order,
    ): void {
        $stateMachine->can($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->willReturn(false);
        $stateMachine->apply($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->shouldNotBeCalled();

        $this
            ->shouldThrow(StateMachineTransitionFailedException::class)
            ->during('cancel', [$order])
        ;
    }
}
