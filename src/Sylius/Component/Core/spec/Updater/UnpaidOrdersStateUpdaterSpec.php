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

use Doctrine\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Psr\Log\LoggerInterface;
use SM\Factory\Factory;
use SM\SMException;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;

final class UnpaidOrdersStateUpdaterSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        Factory $stateMachineFactory,
        LoggerInterface $logger,
        ObjectManager $objectManager,
    ): void {
        $this->beConstructedWith(
            $orderRepository,
            $stateMachineFactory,
            '10 months',
            $logger,
            $objectManager,
            100
        );
    }

    function it_implements_an_expired_orders_state_updater_interface(): void
    {
        $this->shouldImplement(UnpaidOrdersStateUpdaterInterface::class);
    }

    function it_cancels_unpaid_orders(
        Factory $stateMachineFactory,
        ObjectManager $objectManager,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderInterface $thirdOrder,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $firstOrderStateMachine,
        StateMachineInterface $secondOrderStateMachine,
        StateMachineInterface $thirdOrderStateMachine,
    ): void {
        $orderRepository->findOrdersUnpaidSince(Argument::type(\DateTimeInterface::class), 100)->willReturn(
            [$firstOrder, $secondOrder],
            [$thirdOrder],
            [],
        );

        $objectManager->flush()->shouldBeCalledTimes(2);
        $objectManager->clear()->shouldBeCalledTimes(2);

        $stateMachineFactory->get($firstOrder, 'sylius_order')->willReturn($firstOrderStateMachine);
        $stateMachineFactory->get($secondOrder, 'sylius_order')->willReturn($secondOrderStateMachine);
        $stateMachineFactory->get($thirdOrder, 'sylius_order')->willReturn($thirdOrderStateMachine);

        $firstOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();
        $secondOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();
        $thirdOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $this->cancel();
    }

    function it_wont_stop_cancelling_unpaid_orders_on_exception_for_a_single_order_and_logs_error(
        Factory $stateMachineFactory,
        ObjectManager $objectManager,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $firstOrderStateMachine,
        StateMachineInterface $secondOrderStateMachine,
        LoggerInterface $logger,
    ): void {
        $orderRepository->findOrdersUnpaidSince(Argument::type(\DateTimeInterface::class), 100)->willReturn(
            [$firstOrder, $secondOrder],
            [],
        );

        $objectManager->flush()->shouldBeCalledTimes(1);
        $objectManager->clear()->shouldBeCalledTimes(1);

        $firstOrder->getId()->shouldBeCalled()->willReturn(13);

        $stateMachineFactory->get($firstOrder, 'sylius_order')->willReturn($firstOrderStateMachine);
        $stateMachineFactory->get($secondOrder, 'sylius_order')->willReturn($secondOrderStateMachine);

        $firstOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled()
            ->willThrow(new SMException())
        ;
        $secondOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $logger->error(
            Argument::containingString('An error occurred while cancelling unpaid order #13'),
            Argument::any(),
        )->shouldBeCalled();

        $this->cancel();
    }

    function it_wont_stop_cancelling_unpaid_orders_on_exception_for_a_single_order_and_skips_logging_if_logger_is_not_set(
        Factory $stateMachineFactory,
        ObjectManager $objectManager,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $firstOrderStateMachine,
        StateMachineInterface $secondOrderStateMachine,
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachineFactory, '10 months', null, $objectManager);

        $orderRepository->findOrdersUnpaidSince(Argument::type(\DateTimeInterface::class), 100)->willReturn(
            [$firstOrder, $secondOrder],
            [],
        );

        $firstOrder->getId()->shouldNotBeCalled();

        $stateMachineFactory->get($firstOrder, 'sylius_order')->willReturn($firstOrderStateMachine);
        $stateMachineFactory->get($secondOrder, 'sylius_order')->willReturn($secondOrderStateMachine);

        $firstOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled()
            ->willThrow(new SMException())
        ;
        $secondOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $this->cancel();
    }

    function it_wont_batch_processing_unpaid_orders_if_object_manager_is_not_set(
        Factory $stateMachineFactory,
        ObjectManager $objectManager,
        OrderInterface $firstOrder,
        OrderInterface $secondOrder,
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $firstOrderStateMachine,
        StateMachineInterface $secondOrderStateMachine,
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachineFactory, '10 months', null, null, 100);

        $orderRepository->findOrdersUnpaidSince(Argument::type(\DateTimeInterface::class), null)->willReturn(
            [$firstOrder, $secondOrder],
            [],
        );

        $objectManager->flush()->shouldNotBeCalled();
        $objectManager->clear()->shouldNotBeCalled();

        $stateMachineFactory->get($firstOrder, 'sylius_order')->willReturn($firstOrderStateMachine);
        $stateMachineFactory->get($secondOrder, 'sylius_order')->willReturn($secondOrderStateMachine);

        $firstOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();
        $secondOrderStateMachine->apply(OrderTransitions::TRANSITION_CANCEL)->shouldBeCalled();

        $this->cancel();
    }
}
