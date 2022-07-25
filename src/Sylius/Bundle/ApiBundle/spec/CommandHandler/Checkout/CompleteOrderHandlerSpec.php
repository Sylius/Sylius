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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Command\Cart\InformAboutCartRecalculation;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
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
        FactoryInterface $stateMachineFactory,
        MessageBusInterface $commandBus,
        MessageBusInterface $eventBus,
        OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
    ): void {
        $this->beConstructedWith($orderRepository, $stateMachineFactory, $commandBus, $eventBus, $orderPromotionsIntegrityChecker);
    }

    function it_handles_order_completion_without_notes(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        MessageBusInterface $eventBus,
        CustomerInterface $customer,
        OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
    ): void {
        $completeOrder = new CompleteOrder();
        $completeOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn($customer);

        $order->setNotes(null)->shouldNotBeCalled();

        $orderPromotionsIntegrityChecker->check($order)->willReturn(null);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);
        $order->getTokenValue()->willReturn('COMPLETED_ORDER_TOKEN');

        $stateMachine->apply('complete')->shouldBeCalled();

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
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        MessageBusInterface $eventBus,
        CustomerInterface $customer,
        OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
    ): void {
        $completeOrder = new CompleteOrder('ThankYou');
        $completeOrder->setOrderTokenValue('ORDERTOKEN');

        $order->getCustomer()->willReturn($customer);

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->setNotes('ThankYou')->shouldBeCalled();

        $orderPromotionsIntegrityChecker->check($order)->willReturn(null);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can('complete')->willReturn(true);
        $order->getTokenValue()->willReturn('COMPLETED_ORDER_TOKEN');

        $stateMachine->apply('complete')->shouldBeCalled();

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
        OrderInterface $order,
        MessageBusInterface $commandBus,
        CustomerInterface $customer,
        PromotionInterface $promotion,
        OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
    ): void {
        $completeOrder = new CompleteOrder('ThankYou');
        $completeOrder->setOrderTokenValue('ORDERTOKEN');

        $order->getCustomer()->willReturn($customer);

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

    function it_throws_an_exception_if_order_cannot_be_completed(
        OrderRepositoryInterface $orderRepository,
        StateMachineInterface $stateMachine,
        OrderInterface $order,
        FactoryInterface $stateMachineFactory,
        CustomerInterface $customer,
        OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker,
    ): void {
        $completeOrder = new CompleteOrder();
        $completeOrder->setOrderTokenValue('ORDERTOKEN');

        $orderRepository->findOneBy(['tokenValue' => 'ORDERTOKEN'])->willReturn($order);

        $order->getCustomer()->willReturn($customer);

        $orderPromotionsIntegrityChecker->check($order)->willReturn(null);

        $stateMachineFactory->get($order, OrderCheckoutTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(OrderCheckoutTransitions::TRANSITION_COMPLETE)->willReturn(false);

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
