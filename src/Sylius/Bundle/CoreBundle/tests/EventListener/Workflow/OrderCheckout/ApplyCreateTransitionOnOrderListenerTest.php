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

namespace Tests\Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\OrderCheckout\ApplyCreateTransitionOnOrderListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Order\OrderTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class ApplyCreateTransitionOnOrderListenerTest extends TestCase
{
    private MockObject&StateMachineInterface $compositeStateMachine;

    private ApplyCreateTransitionOnOrderListener $listener;

    protected function setUp(): void
    {
        $this->compositeStateMachine = $this->createMock(StateMachineInterface::class);
        $this->listener = new ApplyCreateTransitionOnOrderListener($this->compositeStateMachine);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItDoesNothingIfOrderCannotBeCreated(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->compositeStateMachine
            ->expects($this->once())
            ->method('can')
            ->with($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CREATE)
            ->willReturn(false)
        ;

        $this->compositeStateMachine->expects($this->never())->method('apply');

        ($this->listener)($event);
    }

    public function testItAppliesTransitionCreateOnOrder(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->compositeStateMachine
            ->expects($this->once())
            ->method('can')
            ->with($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CREATE)
            ->willReturn(true)
        ;

        $this->compositeStateMachine
            ->expects($this->once())
            ->method('apply')
            ->with($order, OrderTransitions::GRAPH, OrderTransitions::TRANSITION_CREATE)
        ;

        ($this->listener)($event);
    }
}
