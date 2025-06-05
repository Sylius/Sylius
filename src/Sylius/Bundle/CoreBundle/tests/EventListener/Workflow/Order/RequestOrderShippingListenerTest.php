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

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\RequestOrderShippingListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderShippingTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class RequestOrderShippingListenerTest extends TestCase
{
    private MockObject&StateMachineInterface $compositeStateMachine;

    private RequestOrderShippingListener $listener;

    protected function setUp(): void
    {
        $this->compositeStateMachine = $this->createMock(StateMachineInterface::class);
        $this->listener = new RequestOrderShippingListener($this->compositeStateMachine);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItDoesNothingIfOrderCannotHaveShippingRequested(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->compositeStateMachine
            ->expects($this->once())
            ->method('can')
            ->with(
                $order,
                OrderShippingTransitions::GRAPH,
                OrderShippingTransitions::TRANSITION_REQUEST_SHIPPING,
            )
            ->willReturn(false)
        ;

        $this->compositeStateMachine->expects($this->never())->method('apply');

        ($this->listener)($event);
    }

    public function testItAppliesTransitionRequestShippingOnOrderShipping(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->compositeStateMachine
            ->expects($this->once())
            ->method('can')
            ->with(
                $order,
                OrderShippingTransitions::GRAPH,
                OrderShippingTransitions::TRANSITION_REQUEST_SHIPPING,
            )
            ->willReturn(true)
        ;

        $this->compositeStateMachine
            ->expects($this->once())
            ->method('apply')
            ->with(
                $order,
                OrderShippingTransitions::GRAPH,
                OrderShippingTransitions::TRANSITION_REQUEST_SHIPPING,
            )
        ;

        ($this->listener)($event);
    }
}
