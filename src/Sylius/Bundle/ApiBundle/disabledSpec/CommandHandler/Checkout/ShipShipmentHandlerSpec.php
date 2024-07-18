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
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface as WinzouStateMachineInterface;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\SendShipmentConfirmationEmail;
use Sylius\Bundle\ApiBundle\Command\Checkout\ShipShipment;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Repository\ShipmentRepositoryInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Messenger\Stamp\DispatchAfterCurrentBusStamp;

final class ShipShipmentHandlerSpec extends ObjectBehavior
{
    function let(
        ShipmentRepositoryInterface $shipmentRepository,
        FactoryInterface $stateMachineFactory,
        MessageBusInterface $eventBus,
    ): void {
        $this->beConstructedWith($shipmentRepository, $stateMachineFactory, $eventBus);
    }

    function it_handles_shipping_without_tracking_number(
        ShipmentRepositoryInterface $shipmentRepository,
        WinzouStateMachineInterface $stateMachine,
        ShipmentInterface $shipment,
        FactoryInterface $stateMachineFactory,
        MessageBusInterface $eventBus,
    ): void {
        $shipShipment = new ShipShipment();
        $shipShipment->setShipmentId(123);

        $shipmentRepository->find(123)->willReturn($shipment);

        $shipment->setTracking(null)->shouldNotBeCalled();

        $stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(ShipmentTransitions::TRANSITION_SHIP)->willReturn(true);

        $stateMachine->apply(ShipmentTransitions::TRANSITION_SHIP)->shouldBeCalled();

        $sendShipmentConfirmationEmail = new SendShipmentConfirmationEmail(123);

        $eventBus
            ->dispatch($sendShipmentConfirmationEmail, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($sendShipmentConfirmationEmail))
            ->shouldBeCalled()
        ;

        $this($shipShipment)->shouldReturn($shipment);
    }

    function it_handles_shipping_with_tracking_number(
        ShipmentRepositoryInterface $shipmentRepository,
        WinzouStateMachineInterface $stateMachine,
        ShipmentInterface $shipment,
        FactoryInterface $stateMachineFactory,
        MessageBusInterface $eventBus,
    ): void {
        $shipShipment = new ShipShipment('TRACK');
        $shipShipment->setShipmentId(123);

        $shipmentRepository->find(123)->willReturn($shipment);

        $shipment->setTracking('TRACK')->shouldBeCalled();

        $stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(ShipmentTransitions::TRANSITION_SHIP)->willReturn(true);

        $stateMachine->apply(ShipmentTransitions::TRANSITION_SHIP)->shouldBeCalled();

        $sendShipmentConfirmationEmail = new SendShipmentConfirmationEmail(123);

        $eventBus
            ->dispatch($sendShipmentConfirmationEmail, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($sendShipmentConfirmationEmail))
            ->shouldBeCalled()
        ;

        $this($shipShipment)->shouldReturn($shipment);
    }

    function it_uses_the_new_state_machine_abstraction_if_passed(
        ShipmentRepositoryInterface $shipmentRepository,
        StateMachineInterface $stateMachine,
        MessageBusInterface $eventBus,
        ShipmentInterface $shipment,
    ): void {
        $this->beConstructedWith($shipmentRepository, $stateMachine, $eventBus);

        $shipShipment = new ShipShipment('TRACK');
        $shipShipment->setShipmentId(123);

        $shipmentRepository->find(123)->willReturn($shipment);

        $shipment->setTracking('TRACK')->shouldBeCalled();

        $stateMachine->can($shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_SHIP)->willReturn(true);
        $stateMachine->apply($shipment, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_SHIP)->shouldBeCalled();

        $sendShipmentConfirmationEmail = new SendShipmentConfirmationEmail(123);

        $eventBus
            ->dispatch($sendShipmentConfirmationEmail, [new DispatchAfterCurrentBusStamp()])
            ->willReturn(new Envelope($sendShipmentConfirmationEmail))
            ->shouldBeCalled()
        ;

        $this($shipShipment)->shouldReturn($shipment);
    }

    function it_throws_an_exception_if_shipment_does_not_exist(
        ShipmentRepositoryInterface $shipmentRepository,
    ): void {
        $shipShipment = new ShipShipment('TRACK');
        $shipShipment->setShipmentId(123);

        $shipmentRepository->find(123)->willReturn(null);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$shipShipment])
        ;
    }

    function it_throws_an_exception_if_shipment_cannot_be_shipped(
        ShipmentRepositoryInterface $shipmentRepository,
        WinzouStateMachineInterface $stateMachine,
        ShipmentInterface $shipment,
        FactoryInterface $stateMachineFactory,
    ): void {
        $shipShipment = new ShipShipment('TRACK');
        $shipShipment->setShipmentId(123);

        $shipmentRepository->find(123)->willReturn($shipment);

        $shipment->setTracking('TRACK')->shouldBeCalled();

        $stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->can(ShipmentTransitions::TRANSITION_SHIP)->willReturn(false);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$shipShipment])
        ;
    }
}
