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

namespace spec\Sylius\Bundle\CoreBundle\EventListener\Workflow\Order;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Shipping\ShipmentTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class CancelShipmentListenerSpec extends ObjectBehavior
{
    function let(StateMachineInterface $compositeStateMachine): void
    {
        $this->beConstructedWith($compositeStateMachine);
    }

    function it_throws_an_exception_on_non_supported_subject(\stdClass $callback): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompletedEvent($callback->getWrappedObject(), new Marking())])
        ;
    }

    function it_cancels_shipments(
        StateMachineInterface $compositeStateMachine,
        OrderInterface $order,
        ShipmentInterface $shipment1,
        ShipmentInterface $shipment2,
    ): void {
        $event = new CompletedEvent($order->getWrappedObject(), new Marking());
        $order->getShipments()->willReturn(new ArrayCollection([$shipment1->getWrappedObject(), $shipment2->getWrappedObject()]));

        $this($event);

        $compositeStateMachine
            ->apply($shipment1, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_CANCEL)
            ->shouldHaveBeenCalledOnce()
        ;

        $compositeStateMachine
            ->apply($shipment2, ShipmentTransitions::GRAPH, ShipmentTransitions::TRANSITION_CANCEL)
            ->shouldHaveBeenCalledOnce()
        ;
    }
}
