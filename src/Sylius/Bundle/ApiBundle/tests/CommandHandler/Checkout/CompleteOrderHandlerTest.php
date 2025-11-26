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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Command\Cart\InformAboutCartRecalculation;
use Sylius\Bundle\ApiBundle\Command\Checkout\CompleteOrder;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\CompleteOrderHandler;
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
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class CompleteOrderHandlerTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&StateMachineInterface $stateMachine;

    private MessageBusInterface&MockObject $commandBus;

    private MessageBusInterface&MockObject $eventBus;

    private MockObject&OrderPromotionsIntegrityCheckerInterface $orderPromotionsIntegrityChecker;

    private CompleteOrderHandler $handler;

    private MockObject&OrderInterface $order;

    private CustomerInterface&MockObject $customer;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->commandBus = $this->createMock(MessageBusInterface::class);
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->orderPromotionsIntegrityChecker = $this->createMock(OrderPromotionsIntegrityCheckerInterface::class);
        $this->handler = new CompleteOrderHandler(
            $this->orderRepository,
            $this->stateMachine,
            $this->commandBus,
            $this->eventBus,
            $this->orderPromotionsIntegrityChecker,
        );
        $this->order = $this->createMock(OrderInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);
    }

    public function testHandlesOrderCompletionWithoutNotes(): void
    {
        $this->handler = new CompleteOrderHandler(
            $this->orderRepository,
            $this->stateMachine,
            $this->commandBus,
            $this->eventBus,
            $this->orderPromotionsIntegrityChecker,
        );
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN');
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->order);
        $this->order->expects(self::once())->method('getCustomer')->willReturn($this->customer);
        $this->order->method('getTotal')->willReturn(1500);
        $this->order->expects(self::never())->method('setNotes')->with(null);
        $this->orderPromotionsIntegrityChecker->expects(self::once())->method('check')->with($this->order)->willReturn(null);
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->order, OrderCheckoutTransitions::GRAPH, 'complete')
            ->willReturn(true);
        $this->order->expects(self::once())->method('getTokenValue')->willReturn('COMPLETED_ORDER_TOKEN');
        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with($this->order, OrderCheckoutTransitions::GRAPH, 'complete');
        $orderCompleted = new OrderCompleted('COMPLETED_ORDER_TOKEN');
        $this->eventBus->expects(self::once())
            ->method('dispatch')
            ->with($orderCompleted, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($orderCompleted));
        self::assertSame($this->order, $this->handler->__invoke($completeOrder));
    }

    public function testHandlesOrderCompletionWithNotes(): void
    {
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN', notes: 'ThankYou');
        $this->order->expects(self::once())->method('getCustomer')->willReturn($this->customer);
        $this->order->method('getTotal')->willReturn(1500);
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->order);
        $this->order->expects(self::once())->method('setNotes')->with('ThankYou');
        $this->orderPromotionsIntegrityChecker->expects(self::once())->method('check')->with($this->order)->willReturn(null);
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->order, OrderCheckoutTransitions::GRAPH, 'complete')
            ->willReturn(true);
        $this->order->expects(self::once())->method('getTokenValue')->willReturn('COMPLETED_ORDER_TOKEN');
        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with($this->order, OrderCheckoutTransitions::GRAPH, 'complete');
        $orderCompleted = new OrderCompleted('COMPLETED_ORDER_TOKEN');
        $this->eventBus->expects(self::once())
            ->method('dispatch')
            ->with($orderCompleted, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($orderCompleted));
        self::assertSame($this->order, $this->handler->__invoke($completeOrder));
    }

    public function testDelaysAnInformationAboutCartRecalculate(): void
    {
        /** @var PromotionInterface|MockObject $promotion */
        $promotion = $this->createMock(PromotionInterface::class);
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN', notes: 'ThankYou');
        $this->order->expects(self::once())->method('getCustomer')->willReturn($this->customer);
        $this->order->method('getTotal')->willReturn(1000);
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->order);
        $this->order->expects(self::once())->method('setNotes')->with('ThankYou');
        $this->orderPromotionsIntegrityChecker->expects(self::once())
            ->method('check')
            ->with($this->order)
            ->willReturn($promotion);
        $promotion->expects(self::once())->method('getName')->willReturn('Christmas');
        $informAboutCartRecalculate = new InformAboutCartRecalculation('Christmas');
        $this->commandBus->expects(self::once())
            ->method('dispatch')
            ->with($informAboutCartRecalculate, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($informAboutCartRecalculate));
        self::assertSame($this->order, $this->handler->__invoke($completeOrder));
    }

    public function testThrowsAnExceptionIfOrderDoesNotExist(): void
    {
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN');
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn(null);
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($completeOrder);
    }

    public function testThrowsAnExceptionIfOrderTotalHasChanged(): void
    {
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN');
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->order);
        $this->order->expects(self::once())->method('getCustomer')->willReturn($this->customer);
        $this->order->method('getTotal')->willReturn(1500, 2000);
        $this->orderPromotionsIntegrityChecker->expects(self::once())->method('check')->with($this->order)->willReturn(null);
        self::expectException(OrderTotalHasChangedException::class);
        $this->handler->__invoke($completeOrder);
    }

    public function testThrowsAnExceptionIfOrderCannotBeCompleted(): void
    {
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN');
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->order);
        $this->order->expects(self::once())->method('getCustomer')->willReturn($this->customer);
        $this->order->method('getTotal')->willReturn(1500);
        $this->orderPromotionsIntegrityChecker->expects(self::once())->method('check')->with($this->order)->willReturn(null);
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->order, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_COMPLETE)
            ->willReturn(false);
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($completeOrder);
    }

    public function testThrowsAnExceptionIfOrderCustomerIsNull(): void
    {
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN');
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'ORDERTOKEN'])
            ->willReturn($this->order);
        $this->order->expects(self::once())->method('getCustomer')->willReturn(null);
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($completeOrder);
    }
}
