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
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\RequestOrderPaymentListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderPaymentTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class RequestOrderPaymentListenerTest extends TestCase
{
    private MockObject&StateMachineInterface $compositeStateMachine;

    private RequestOrderPaymentListener $listener;

    protected function setUp(): void
    {
        $this->compositeStateMachine = $this->createMock(StateMachineInterface::class);
        $this->listener = new RequestOrderPaymentListener($this->compositeStateMachine);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItDoesNothingIfOrderCannotHavePaymentRequested(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->compositeStateMachine
            ->expects($this->once())
            ->method('can')
            ->with(
                $order,
                OrderPaymentTransitions::GRAPH,
                OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT,
            )
            ->willReturn(false)
        ;

        $this->compositeStateMachine->expects($this->never())->method('apply');

        ($this->listener)($event);
    }

    public function testItAppliesTransitionRequestPaymentOnOrderPayment(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $event = new CompletedEvent($order, new Marking());

        $this->compositeStateMachine
            ->expects($this->once())
            ->method('can')
            ->with(
                $order,
                OrderPaymentTransitions::GRAPH,
                OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT,
            )
            ->willReturn(true)
        ;

        $this->compositeStateMachine
            ->expects($this->once())
            ->method('apply')
            ->with(
                $order,
                OrderPaymentTransitions::GRAPH,
                OrderPaymentTransitions::TRANSITION_REQUEST_PAYMENT,
            )
        ;

        ($this->listener)($event);
    }
}
