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

namespace Tests\Sylius\Component\Core\Updater;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;
use Sylius\Abstraction\StateMachine\Exception\StateMachineExecutionException;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdater;
use Sylius\Component\Core\Updater\UnpaidOrdersStateUpdaterInterface;
use Sylius\Component\Order\OrderTransitions;

final class UnpaidOrdersStateUpdaterTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&StateMachineInterface $stateMachine;

    private LoggerInterface&MockObject $logger;

    private MockObject&ObjectManager $objectManager;

    private MockObject&OrderInterface $firstOrder;

    private MockObject&OrderInterface $secondOrder;

    private UnpaidOrdersStateUpdater $updater;

    protected function setUp(): void
    {
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->logger = $this->createMock(LoggerInterface::class);
        $this->objectManager = $this->createMock(ObjectManager::class);
        $this->firstOrder = $this->createMock(OrderInterface::class);
        $this->secondOrder = $this->createMock(OrderInterface::class);
        $this->updater = new UnpaidOrdersStateUpdater(
            $this->orderRepository,
            $this->stateMachine,
            '10 months',
            $this->logger,
            $this->objectManager,
            100,
        );
    }

    public function testShouldImplementExpiredOrdersStateUpdaterInterface(): void
    {
        $this->assertInstanceOf(UnpaidOrdersStateUpdaterInterface::class, $this->updater);
    }

    public function testShouldCancelUnpaidOrders(): void
    {
        $thirdOrder = $this->createMock(OrderInterface::class);
        $this->orderRepository
            ->expects($this->exactly(3))
            ->method('findOrdersUnpaidSince')
            ->with($this->isInstanceOf(\DateTimeInterface::class), 100)
            ->willReturnOnConsecutiveCalls(
                [$this->firstOrder, $this->secondOrder],
                [$thirdOrder],
                [],
            );
        $this->objectManager->expects($this->exactly(2))->method('flush');
        $this->objectManager->expects($this->exactly(2))->method('clear');
        $expectedCalls = [
            [$this->firstOrder, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL],
            [$this->secondOrder, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL],
            [$thirdOrder, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CANCEL],
        ];
        $this->stateMachine
            ->expects($this->exactly(3))
            ->method('apply')
            ->willReturnCallback(function (...$args) use (&$expectedCalls) {
                $expected = array_shift($expectedCalls);
                $this->assertSame($expected[0], $args[0]);
                $this->assertSame($expected[1], $args[1]);
                $this->assertSame($expected[2], $args[2]);
            });

        $this->updater->cancel();
    }

    public function testShouldNotStopCancellingUnpaidOrdersOnExceptionForSingleOrderAndLogsError(): void
    {
        $this->orderRepository
            ->expects($this->exactly(2))
            ->method('findOrdersUnpaidSince')
            ->with($this->isInstanceOf(\DateTimeInterface::class), 100)
            ->willReturnOnConsecutiveCalls(
                [$this->firstOrder, $this->secondOrder],
                [],
            );
        $this->objectManager->expects($this->once())->method('flush');
        $this->objectManager->expects($this->once())->method('clear');
        $this->firstOrder->expects($this->once())->method('getId')->willReturn(13);
        $this->stateMachine
            ->expects($this->exactly(2))
            ->method('apply')
            ->willReturnCallback(function ($order, $graph, $transition) {
                if ($order === $this->firstOrder) {
                    throw new StateMachineExecutionException('Simulated failure');
                }

                $this->assertSame($this->secondOrder, $order);
                $this->assertSame(OrderTransitions::GRAPH, $graph);
                $this->assertSame(OrderTransitions::TRANSITION_CANCEL, $transition);
            });
        $this->logger
            ->expects($this->once())
            ->method('error')
            ->with(
                $this->stringContains('An error occurred while cancelling unpaid order #13'),
                $this->anything(),
            );

        $this->updater->cancel();
    }
}
