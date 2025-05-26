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

namespace Tests\Sylius\Component\Core\StateResolver;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\OrderShippingStates;
use Sylius\Component\Core\OrderShippingTransitions;
use Sylius\Component\Core\StateResolver\OrderShippingStateResolver;
use Sylius\Component\Order\StateResolver\StateResolverInterface;

final class OrderShippingStateResolverTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private MockObject&OrderInterface $order;

    private MockObject&ShipmentInterface $firstShipment;

    private MockObject&ShipmentInterface $secondShipment;

    private OrderShippingStateResolver $stateResolver;

    protected function setUp(): void
    {
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->firstShipment = $this->createMock(ShipmentInterface::class);
        $this->secondShipment = $this->createMock(ShipmentInterface::class);
        $this->stateResolver = new OrderShippingStateResolver($this->stateMachine);
    }

    public function testShouldImplementStateResolverInterface(): void
    {
        $this->assertInstanceOf(StateResolverInterface::class, $this->stateResolver);
    }

    public function testShouldMarksOrderAsShippedIfAllShipmentsDelivered(): void
    {
        $this->order->expects($this->exactly(4))->method('getShipments')->willReturn(new ArrayCollection([
            $this->firstShipment,
            $this->secondShipment,
        ]));
        $this->order->expects($this->exactly(2))->method('getShippingState')->willReturn(OrderShippingStates::STATE_READY);
        $this->firstShipment->expects($this->exactly(2))->method('getState')->willReturn(ShipmentInterface::STATE_SHIPPED);
        $this->secondShipment->expects($this->exactly(2))->method('getState')->willReturn(ShipmentInterface::STATE_SHIPPED);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_SHIP);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarkOrderAsShippedIfThereAreNoShipmentsToDelivery(): void
    {
        $this->order->expects($this->exactly(4))->method('getShipments')->willReturn(new ArrayCollection());
        $this->order->expects($this->exactly(2))->method('getShippingState')->willReturn(OrderShippingStates::STATE_READY);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_SHIP);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldMarkOrderAsPartiallyShippedIfSomeShipmentsAreDelivered(): void
    {
        $this->order->expects($this->exactly(4))->method('getShipments')->willReturn(new ArrayCollection([
            $this->firstShipment,
            $this->secondShipment,
        ]));
        $this->order->expects($this->exactly(2))->method('getShippingState')->willReturn(OrderShippingStates::STATE_READY);
        $this->firstShipment->expects($this->exactly(2))->method('getState')->willReturn(ShipmentInterface::STATE_SHIPPED);
        $this->secondShipment->expects($this->exactly(2))->method('getState')->willReturn(ShipmentInterface::STATE_CANCELLED);
        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($this->order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_PARTIALLY_SHIP);

        $this->stateResolver->resolve($this->order);
    }

    public function testShouldNotMarkOrderIfItIsAlreadyInShippingState(): void
    {
        $this->order->expects($this->once())->method('getShippingState')->willReturn(OrderShippingStates::STATE_SHIPPED);
        $this->stateMachine->expects($this->never())->method('apply')->with($this->anything());

        $this->stateResolver->resolve($this->order);
    }
}
