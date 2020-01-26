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

namespace spec\Sylius\Bundle\AdminBundle\Shipper;

use Doctrine\Common\Persistence\ObjectManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Resource\Exception\UpdateHandlingException;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class OrderShipmentShipperSpec extends ObjectBehavior
{
    function let(
        StateMachineFactoryInterface $stateMachineFactory,
        ObjectManager $shipmentManager,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $this->beConstructedWith($stateMachineFactory, $shipmentManager, $eventDispatcher);
    }

    function it_ships_an_shipment_with_tracking_code(
        ShipmentInterface $shipment,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        EventDispatcherInterface $eventDispatcher,
        ObjectManager $shipmentManager
    ): void {
        $stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($stateMachine->getWrappedObject());
        $stateMachine->can(ShipmentTransitions::TRANSITION_SHIP)->willReturn(true);

        $shipment->setTracking('AABB-BBTT')->shouldBeCalled();

        $stateMachine->apply(ShipmentTransitions::TRANSITION_SHIP)->shouldBeCalled();

        $eventDispatcher->dispatch('sylius.shipment.post_ship', Argument::type(GenericEvent::class))->shouldBeCalled();
        $shipmentManager->flush()->shouldBeCalled();

        $this->ship($shipment, 'AABB-BBTT');
    }

    function it_ships_an_shipment_without_tracking_code(
        ShipmentInterface $shipment,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine,
        EventDispatcherInterface $eventDispatcher,
        ObjectManager $shipmentManager
    ): void {
        $stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($stateMachine->getWrappedObject());
        $stateMachine->can(ShipmentTransitions::TRANSITION_SHIP)->willReturn(true);

        $shipment->setTracking('AABB-BBTT')->shouldNotBeCalled();

        $stateMachine->apply(ShipmentTransitions::TRANSITION_SHIP)->shouldBeCalled();

        $eventDispatcher->dispatch('sylius.shipment.post_ship', Argument::type(GenericEvent::class))->shouldBeCalled();
        $shipmentManager->flush()->shouldBeCalled();

        $this->ship($shipment);
    }

    function it_throws_an_update_handling_exception_if_transition_is_not_possible(
        ShipmentInterface $shipment,
        StateMachineFactoryInterface $stateMachineFactory,
        StateMachineInterface $stateMachine
    ): void {
        $stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($stateMachine->getWrappedObject());
        $stateMachine->can(ShipmentTransitions::TRANSITION_SHIP)->willReturn(false);

        $this->shouldThrow(new UpdateHandlingException())->during('ship', [$shipment]);
    }
}
