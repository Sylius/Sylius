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
use Sylius\Bundle\ApiBundle\Command\Checkout\SendShipmentConfirmationEmail;
use Sylius\Bundle\ApiBundle\Command\Checkout\ShipShipment;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\ShipShipmentHandler;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class ShipShipmentHandlerTest extends TestCase
{
    private MockObject&ShipmentRepositoryInterface $shipmentRepository;

    private MockObject&StateMachineInterface $stateMachine;

    private MessageBusInterface&MockObject $eventBus;

    private ShipShipmentHandler $handler;

    private MockObject&ShipmentInterface $shipment;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->shipmentRepository = $this->createMock(ShipmentRepositoryInterface::class);
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->eventBus = $this->createMock(MessageBusInterface::class);
        $this->handler = new ShipShipmentHandler($this->shipmentRepository, $this->stateMachine, $this->eventBus);
        $this->shipment = $this->createMock(ShipmentInterface::class);
    }

    public function testHandlesShippingWithoutTrackingNumber(): void
    {
        $shipShipment = new ShipShipment(shipmentId: 123);
        $this->shipmentRepository->expects(self::once())->method('find')->with(123)->willReturn($this->shipment);
        $this->shipment->expects(self::never())->method('setTracking')->with(null);
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_SHIP)
            ->willReturn(true);
        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with($this->shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_SHIP);
        $sendShipmentConfirmationEmail = new SendShipmentConfirmationEmail(123);
        $this->eventBus->expects(self::once())
            ->method('dispatch')
            ->with($sendShipmentConfirmationEmail, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($sendShipmentConfirmationEmail));
        self::assertSame($this->shipment, $this->handler->__invoke($shipShipment));
    }

    public function testHandlesShippingWithTrackingNumber(): void
    {
        $shipShipment = new ShipShipment(shipmentId: 123, trackingCode: 'TRACK');
        $this->shipmentRepository->expects(self::once())->method('find')->with(123)->willReturn($this->shipment);
        $this->shipment->expects(self::once())->method('setTracking')->with('TRACK');
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_SHIP)
            ->willReturn(true);
        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with($this->shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_SHIP);
        $sendShipmentConfirmationEmail = new SendShipmentConfirmationEmail(123);
        $this->eventBus->expects(self::once())
            ->method('dispatch')
            ->with($sendShipmentConfirmationEmail, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($sendShipmentConfirmationEmail));
        self::assertSame($this->shipment, $this->handler->__invoke($shipShipment));
    }

    public function testThrowsAnExceptionIfShipmentDoesNotExist(): void
    {
        $shipShipment = new ShipShipment(shipmentId: 123, trackingCode: 'TRACK');
        $this->shipmentRepository->expects(self::once())->method('find')->with(123)->willReturn(null);
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($shipShipment);
    }

    public function testThrowsAnExceptionIfShipmentCannotBeShipped(): void
    {
        $shipShipment = new ShipShipment(shipmentId: 123, trackingCode: 'TRACK');
        $this->shipmentRepository->expects(self::once())->method('find')->with(123)->willReturn($this->shipment);
        $this->shipment->expects(self::once())->method('setTracking')->with('TRACK');
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($this->shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_SHIP)
            ->willReturn(false);
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($shipShipment);
    }
}
