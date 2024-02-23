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

use PhpSpec\ObjectBehavior;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderShippingTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class RequestOrderShippingListenerSpec extends ObjectBehavior
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

    function it_does_nothing_if_order_cannot_have_shipping_requested(
        StateMachineInterface $compositeStateMachine,
        OrderInterface $order,
    ): void {
        $event = new CompletedEvent($order->getWrappedObject(), new Marking());

        $compositeStateMachine
            ->can($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_REQUEST_SHIPPING)
            ->willReturn(false)
        ;

        $this($event);

        $compositeStateMachine
            ->apply($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_REQUEST_SHIPPING)
            ->shouldNotHaveBeenCalled()
        ;
    }

    function it_applies_transition_request_shipping_on_order_shipping(
        StateMachineInterface $compositeStateMachine,
        OrderInterface $order,
    ): void {
        $event = new CompletedEvent($order->getWrappedObject(), new Marking());

        $compositeStateMachine
            ->can($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_REQUEST_SHIPPING)
            ->willReturn(true)
        ;

        $this($event);

        $compositeStateMachine
            ->apply($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_REQUEST_SHIPPING)
            ->shouldBeCalled()
        ;
    }
}
