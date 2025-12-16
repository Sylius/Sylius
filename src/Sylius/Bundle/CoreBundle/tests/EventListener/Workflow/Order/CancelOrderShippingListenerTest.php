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
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelOrderShippingListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderShippingTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class CancelOrderShippingListenerTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private CancelOrderShippingListener $listener;

    protected function setUp(): void
    {
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->listener = new CancelOrderShippingListener($this->stateMachine);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItDoesNothingIfOrderCannotHaveShippingCancelled(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_CANCEL)
            ->willReturn(false)
        ;

        $this->stateMachine->expects($this->never())->method('apply');

        ($this->listener)($event);
    }

    public function testItAppliesTransitionCancelOnOrderShipping(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_CANCEL)
            ->willReturn(true)
        ;

        $this->stateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($order, OrderShippingTransitions::GRAPH, OrderShippingTransitions::TRANSITION_CANCEL)
        ;

        ($this->listener)($event);
    }
}
