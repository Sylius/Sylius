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
use SM\StateMachine\StateMachine;
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
        StateMachine $stateMachine,
    ): void {
        $stateMachineFactory->get($order, OrderTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $this->cancel($order);
    }
}
