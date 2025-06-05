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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener\Workflow\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CreateShipmentListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class CreateShipmentListenerTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private CreateShipmentListener $listener;

    protected function setUp(): void
    {
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->listener = new CreateShipmentListener($this->stateMachine);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItDoesNothingIfShipmentCannotBeCreated(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $shipment = $this->createMock(ShipmentInterface::class);

        $order->method('getShipments')->willReturn(new ArrayCollection([$shipment]));

        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_CREATE)
            ->willReturn(false)
        ;

        $this->stateMachine->expects($this->never())->method('apply');

        $event = new CompletedEvent($order, new Marking());
        ($this->listener)($event);
    }

    public function testItAppliesTransitionCreateOnShipments(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $shipment1 = $this->createMock(ShipmentInterface::class);
        $shipment2 = $this->createMock(ShipmentInterface::class);

        $order->method('getShipments')->willReturn(new ArrayCollection([$shipment1, $shipment2]));

        $this->stateMachine
            ->method('can')
            ->willReturnCallback(
                fn ($subject, $graph, $transition) => in_array($subject, [$shipment1, $shipment2], true),
            )
        ;

        $this->stateMachine
            ->expects($this->exactly(2))
            ->method('apply')
            ->with(
                $this->logicalOr(
                    $this->identicalTo($shipment1),
                    $this->identicalTo($shipment2),
                ),
                ShipmentTransitions::GRAPH,
                ShipmentTransitions::TRANSITION_CREATE,
            )
        ;

        $event = new CompletedEvent($order, new Marking());
        ($this->listener)($event);
    }
}
