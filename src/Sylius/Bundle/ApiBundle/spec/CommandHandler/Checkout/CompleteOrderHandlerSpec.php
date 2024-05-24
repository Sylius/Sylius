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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use PhpSpec\ObjectBehavior;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Command\Cart\InformAboutCartRecalculation;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\Exception\OrderTotalHasChangedException;
use Sylius\Bundle\ApiBundle\Event\OrderCompleted;
use Sylius\Bundle\CoreBundle\Order\Checker\OrderPromotionsIntegrityCheckerInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class CompleteOrderHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        MessageBusInterface $commandBus,
        MessageBusInterface $eventBus,
        OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachine, $commandBus, $eventBus, $orderPromotionsIntegrityChecker);
    }

    function it_handles_order_completion_without_notes(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        MessageBusInterface $commandBus,
        MessageBusInterface $eventBus,
        OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
        OrderInterface $order,
        CustomerInterface $customer,
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachine, $commandBus, $eventBus, $orderPromotionsIntegrityChecker);

        $completeOrder = new CompleteOrder();
        $completeOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn($customer);
        $order->getTotal()->willReturn(1500);

        $order->setNotes(null)->shouldNotBeCalled();

        $orderPromotionsIntegrityChecker->check($order)->willReturn(null);

        $stateMachine->can($order, OrderCheckoutTransitions::GRAPH, 'complete')->willReturn(true);
        $order->getTokenValue()->willReturn('COMPLETED_ORDER_TOKEN');

        $stateMachine->apply($order, OrderCheckoutTransitions::GRAPH, 'complete')->shouldBeCalled();

        $orderCompleted = new OrderCompleted('COMPLETED_ORDER_TOKEN');

        $eventBus
            ->dispatch($orderCompleted, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($orderCompleted))
            ->shouldBeCalled()
        ;

        $this($completeOrder)->shouldReturn($order);
    }

    function it_handles_order_completion_with_notes(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        MessageBusInterface $eventBus,
        OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
        OrderInterface $order,
        CustomerInterface $customer,
    ): void {
        $completeOrder = new CompleteOrder('ThankYou');
        $completeOrder->setOrderTokenValue('ORDERTOKEN');

        $order->getCustomer()->willReturn($customer);
        $order->getTotal()->willReturn(1500);

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->setNotes('ThankYou')->shouldBeCalled();

        $orderPromotionsIntegrityChecker->check($order)->willReturn(null);

        $stateMachine->can($order, OrderCheckoutTransitions::GRAPH, 'complete')->willReturn(true);
        $order->getTokenValue()->willReturn('COMPLETED_ORDER_TOKEN');

        $stateMachine->apply($order, OrderCheckoutTransitions::GRAPH, 'complete')->shouldBeCalled();

        $orderCompleted = new OrderCompleted('COMPLETED_ORDER_TOKEN');

        $eventBus
            ->dispatch($orderCompleted, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($orderCompleted))
            ->shouldBeCalled()
        ;

        $this($completeOrder)->shouldReturn($order);
    }

    function it_delays_an_information_about_cart_recalculate(
        OrderRepositoryInterface $orderRepository,
        MessageBusInterface $commandBus,
        OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
        OrderInterface $order,
        CustomerInterface $customer,
        PromotionInterface $promotion,
    ): void {
        $completeOrder = new CompleteOrder('ThankYou');
        $completeOrder->setOrderTokenValue('ORDERTOKEN');

        $order->getCustomer()->willReturn($customer);
        $order->getTotal()->willReturn(1000);

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->setNotes('ThankYou')->shouldBeCalled();

        $orderPromotionsIntegrityChecker->check($order)->willReturn($promotion);
        $promotion->getName()->willReturn('Christmas');

        $informAboutCartRecalculate = new InformAboutCartRecalculation('Christmas');

        $commandBus
            ->dispatch($informAboutCartRecalculate, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($informAboutCartRecalculate))
            ->shouldBeCalled()
        ;

        $this($completeOrder)->shouldReturn($order);
    }

    function it_throws_an_exception_if_order_does_not_exist(
        OrderRepositoryInterface $orderRepository,
    ): void {
        $completeOrder = new CompleteOrder();
        $completeOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$completeOrder])
        ;
    }

    function it_throws_an_exception_if_order_total_has_changed(
        OrderRepositoryInterface $orderRepository,
        OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
        OrderInterface $order,
        CustomerInterface $customer,
    ): void {
        $completeOrder = new CompleteOrder();
        $completeOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn($customer);
        $order->getTotal()->willReturn(1500, 2000);

        $orderPromotionsIntegrityChecker->check($order)->willReturn(null);

        $this
            ->shouldThrow(OrderTotalHasChangedException::class)
            ->during('__invoke', [$completeOrder])
        ;
    }

    function it_throws_an_exception_if_order_cannot_be_completed(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
        OrderInterface $order,
        CustomerInterface $customer,
    ): void {
        $completeOrder = new CompleteOrder();
        $completeOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn($customer);
        $order->getTotal()->willReturn(1500);

        $orderPromotionsIntegrityChecker->check($order)->willReturn(null);

        $stateMachine->can($order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_COMPLETE)->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$completeOrder])
        ;
    }

    function it_throws_an_exception_if_order_customer_is_null(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
    ): void {
        $completeOrder = new CompleteOrder();
        $completeOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$completeOrder])
        ;
    }
}
