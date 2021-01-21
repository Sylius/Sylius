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

namespace spec\Sylius\Bundle\ApiBundle\Applicator;

use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use SM\StateMachine\StateMachine;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\GenericEvent;

final class ShipmentStateMachineTransitionApplicatorSpec extends ObjectBehavior
{
    function let(StateMachineFactoryInterface $stateMachineFactory, EventDispatcherInterface $eventDispatcher): void
    {
        $this->beConstructedWith($stateMachineFactory, $eventDispatcher);
    }

    function it_ships_shipment_and_sends_emails(
        StateMachineFactoryInterface $stateMachineFactory,
        ShipmentInterface $shipment,
        StateMachine $stateMachine,
        EventDispatcherInterface $eventDispatcher
    ): void {
        $stateMachineFactory->get($shipment, ShipmentTransitions::GRAPH)->willReturn($stateMachine);
        $stateMachine->apply(ShipmentTransitions::TRANSITION_SHIP)->shouldBeCalled();

        $eventDispatcher->dispatch(new GenericEvent($shipment->getWrappedObject()), 'sylius.shipment.post_ship')->shouldBeCalled();

        $this->ship($shipment);
    }
}
