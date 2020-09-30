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

namespace spec\Sylius\Component\Core\Updater;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use SM\Factory\Factory;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

final class UnpaidOrdersStateUpdaterSpec extends ObjectBehavior
{
    function let(OrderRepositoryInterface $orderRepository, Factory $stateMachineFactory, LoggerInterface $logger): void
    {
        $this->beConstructedWith($orderRepository, $stateMachineFactory, '10 months', $logger);
    }

    function it_implements_an_expired_orders_state_updater_interface(): void
    {
        $this->shouldImplement(UnpaidOrdersStateUpdaterInterface::class);
    }

    function it_cancels_unpaid_orders(
        Factory $stateMachineFactory,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $firstOrderStateMachine,
        StateMachineInterface $secondOrderStateMachine
    ): void {
        $orderRepository->findOrdersUnpaidSince(Argument::type(\DateTimeInterface::class))->willReturn([
           $firstOrder,
           $secondOrder,
        ]);

        $stateMachineFactory->get($firstOrder, 'sylius_order')->willReturn($firstOrderStateMachine);
        $stateMachineFactory->get($secondOrder, 'sylius_order')->willReturn($secondOrderStateMachine);

        $firstOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();
        $secondOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $this->cancel();
    }

    function it_wont_stop_cancelling_unpaid_orders_on_exception_during_cancelling_a_single_order(
        Factory $stateMachineFactory,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $firstOrderStateMachine,
        StateMachineInterface $secondOrderStateMachine,
        LoggerInterface $logger
    ): void {
        $orderRepository->findOrdersUnpaidSince(Argument::type(\DateTimeInterface::class))->willReturn([
            $firstOrder,
            $secondOrder,
        ]);

        $firstOrder->getId()->shouldBeCalled()->willReturn(13);

        $stateMachineFactory->get($firstOrder, 'sylius_order')->willReturn($firstOrderStateMachine);
        $stateMachineFactory->get($secondOrder, 'sylius_order')->willReturn($secondOrderStateMachine);

        $firstOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled()
            ->willThrow(new \Exception());
        $secondOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $logger->error(
            Argument::containingString('An error occurred while cancelling unpaid order #13'),
            Argument::any()
        )->shouldBeCalled();

        $this->cancel();
    }
}
