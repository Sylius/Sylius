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

namespace spec\Sylius\Component\Core\Updater;

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

final class UnpaidOrdersStateUpdaterSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        LoggerInterface $logger,
        ObjectManager $objectManager,
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $stateMachine,
            '10 months',
            $logger,
            $objectManager,
            100,
        );
    }

    function it_implements_an_expired_orders_state_updater_interface(): void
    {
        $this->shouldImplement(UnpaidOrdersStateUpdaterInterface::class);
    }

    function it_cancels_unpaid_orders(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        ObjectManager $objectManager,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderInterface $thirdOrder,
    ): void {
        $orderRepository->findOrdersUnpaidSince(Argument::type(\DateTimeInterface::class), 100)->willReturn(
            [$firstOrder, $secondOrder],
            [$thirdOrder],
            [],
        );

        $objectManager->flush()->shouldBeCalledTimes(2);
        $objectManager->clear()->shouldBeCalledTimes(2);

        $stateMachine->apply($firstOrder, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();
        $stateMachine->apply($secondOrder, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();
        $stateMachine->apply($thirdOrder, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $this->cancel();
    }

    function it_wont_stop_cancelling_unpaid_orders_on_exception_for_a_single_order_and_logs_error(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        ObjectManager $objectManager,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        LoggerInterface $logger,
    ): void {
        $orderRepository->findOrdersUnpaidSince(Argument::type(\DateTimeInterface::class), 100)->willReturn(
            [$firstOrder, $secondOrder],
            [],
        );

        $objectManager->flush()->shouldBeCalledTimes(1);
        $objectManager->clear()->shouldBeCalledTimes(1);

        $firstOrder->getId()->shouldBeCalled()->willReturn(13);

        $stateMachine
            ->apply($firstOrder, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled()
            ->willThrow(StateMachineExecutionException::class)
        ;
        $stateMachine
            ->apply($secondOrder, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL)
            ->shouldBeCalled()
        ;

        $logger->error(
            Argument::containingString('An error occurred while cancelling unpaid order #13'),
            Argument::any(),
        )->shouldBeCalled();

        $this->cancel();
    }
}
