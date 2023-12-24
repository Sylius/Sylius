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

namespace spec\Sylius\Bundle\CoreBundle\EventListener\Workflow\Shipment;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\Order;
use Sylius\Component\Core\Model\Shipment;
use Sylius\Component\Order\StateResolver\StateResolverInterface;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ResolveOrderShipmentStateListenerSpec extends ObjectBehavior
{
    function let(StateResolverInterface $stateResolver): void
    {
        $this->beConstructedWith($stateResolver);
    }

    function it_throws_an_exception_on_non_supported_subject(\stdClass $subject): void
    {
        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [new CompletedEvent($subject->getWrappedObject(), new Marking())])
        ;
    }

    function it_resolves_order_shipment_state_after_order_being_shipped(
        StateResolverInterface $stateResolver,
    ): void {
        $shipment = new Shipment();
        $order = new Order();
        $shipment->setOrder($order);

        $event = new CompletedEvent($shipment, new Marking());

        $stateResolver->resolve($order)->shouldBeCalled();

        $this->__invoke($event);
    }
}
