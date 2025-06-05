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

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
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

final class CompleteOrderHandlerTest extends TestCase
{
    /** @var OrderRepositoryInterface|MockObject */
    private MockObject $orderRepositoryMock;

    /** @var StateMachineInterface|MockObject */
    private MockObject $stateMachineMock;

    /** @var MessageBusInterface|MockObject */
    private MockObject $commandBusMock;

    /** @var MessageBusInterface|MockObject */
    private MockObject $eventBusMock;

    /** @var OrderPromotionsIntegrityCheckerInterface|MockObject */
    private MockObject $orderPromotionsIntegrityCheckerMock;

    private CompleteOrderHandler $completeOrderHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->stateMachineMock = $this->createMock(StateMachineInterface::class);
        $this->commandBusMock = $this->createMock(MessageBusInterface::class);
        $this->eventBusMock = $this->createMock(MessageBusInterface::class);
        $this->orderPromotionsIntegrityCheckerMock = $this->createMock(OrderPromotionsIntegrityCheckerInterface::class);
        $this->completeOrderHandler = new CompleteOrderHandler($this->orderRepositoryMock, $this->stateMachineMock, $this->commandBusMock, $this->eventBusMock, $this->orderPromotionsIntegrityCheckerMock);
    }

    public function testHandlesOrderCompletionWithoutNotes(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $this->completeOrderHandler = new CompleteOrderHandler($this->orderRepositoryMock, $this->stateMachineMock, $this->commandBusMock, $this->eventBusMock, $this->orderPromotionsIntegrityCheckerMock);
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN');
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $orderMock->expects(self::once())->method('getTotal')->willReturn(1500);
        $orderMock->expects(self::never())->method('setNotes')->with(null);
        $this->orderPromotionsIntegrityCheckerMock->expects(self::once())->method('check')->with($orderMock)->willReturn(null);
        $this->stateMachineMock->expects(self::once())->method('can')->with($orderMock, OrderCheckoutTransitions::GRAPH, 'complete')->willReturn(true);
        $orderMock->expects(self::once())->method('getTokenValue')->willReturn('COMPLETED_ORDER_TOKEN');
        $this->stateMachineMock->expects(self::once())->method('apply')->with($orderMock, OrderCheckoutTransitions::GRAPH, 'complete');
        $orderCompleted = new OrderCompleted('COMPLETED_ORDER_TOKEN');
        $this->eventBusMock->expects(self::once())->method('dispatch')->with($orderCompleted, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($orderCompleted))
        ;
        self::assertSame($orderMock, $this($completeOrder));
    }

    public function testHandlesOrderCompletionWithNotes(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN', notes: 'ThankYou');
        $orderMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $orderMock->expects(self::once())->method('getTotal')->willReturn(1500);
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($orderMock);
        $orderMock->expects(self::once())->method('setNotes')->with('ThankYou');
        $this->orderPromotionsIntegrityCheckerMock->expects(self::once())->method('check')->with($orderMock)->willReturn(null);
        $this->stateMachineMock->expects(self::once())->method('can')->with($orderMock, OrderCheckoutTransitions::GRAPH, 'complete')->willReturn(true);
        $orderMock->expects(self::once())->method('getTokenValue')->willReturn('COMPLETED_ORDER_TOKEN');
        $this->stateMachineMock->expects(self::once())->method('apply')->with($orderMock, OrderCheckoutTransitions::GRAPH, 'complete');
        $orderCompleted = new OrderCompleted('COMPLETED_ORDER_TOKEN');
        $this->eventBusMock->expects(self::once())->method('dispatch')->with($orderCompleted, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($orderCompleted))
        ;
        self::assertSame($orderMock, $this($completeOrder));
    }

    public function testDelaysAnInformationAboutCartRecalculate(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        /** @var PromotionInterface|MockObject $promotionMock */
        $promotionMock = $this->createMock(PromotionInterface::class);
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN', notes: 'ThankYou');
        $orderMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $orderMock->expects(self::once())->method('getTotal')->willReturn(1000);
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($orderMock);
        $orderMock->expects(self::once())->method('setNotes')->with('ThankYou');
        $this->orderPromotionsIntegrityCheckerMock->expects(self::once())->method('check')->with($orderMock)->willReturn($promotionMock);
        $promotionMock->expects(self::once())->method('getName')->willReturn('Christmas');
        $informAboutCartRecalculate = new InformAboutCartRecalculation('Christmas');
        $this->commandBusMock->expects(self::once())->method('dispatch')->with($informAboutCartRecalculate, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($informAboutCartRecalculate))
        ;
        self::assertSame($orderMock, $this($completeOrder));
    }

    public function testThrowsAnExceptionIfOrderDoesNotExist(): void
    {
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN');
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn(null);
        $this->expectException(InvalidArgumentException::class);
        $this->completeOrderHandler->__invoke($completeOrder);
    }

    public function testThrowsAnExceptionIfOrderTotalHasChanged(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN');
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $orderMock->expects(self::once())->method('getTotal')->willReturn(1500, 2000);
        $this->orderPromotionsIntegrityCheckerMock->expects(self::once())->method('check')->with($orderMock)->willReturn(null);
        $this->expectException(OrderTotalHasChangedException::class);
        $this->completeOrderHandler->__invoke($completeOrder);
    }

    public function testThrowsAnExceptionIfOrderCannotBeCompleted(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN');
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getCustomer')->willReturn($customerMock);
        $orderMock->expects(self::once())->method('getTotal')->willReturn(1500);
        $this->orderPromotionsIntegrityCheckerMock->expects(self::once())->method('check')->with($orderMock)->willReturn(null);
        $this->stateMachineMock->expects(self::once())->method('can')->with($orderMock, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_COMPLETE)->willReturn(false);
        $this->expectException(InvalidArgumentException::class);
        $this->completeOrderHandler->__invoke($completeOrder);
    }

    public function testThrowsAnExceptionIfOrderCustomerIsNull(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $completeOrder = new CompleteOrder(orderTokenValue: 'ORDERTOKEN');
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'ORDERTOKEN'])->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getCustomer')->willReturn(null);
        $this->expectException(InvalidArgumentException::class);
        $this->completeOrderHandler->__invoke($completeOrder);
    }
}
