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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CancelPaymentListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class CancelPaymentListenerTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private CancelPaymentListener $listener;

    protected function setUp(): void
    {
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->listener = new CancelPaymentListener($this->stateMachine);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItDoesNothingIfPaymentCannotBeCancelled(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $payment = $this->createMock(PaymentInterface::class);
        $order->method('getPayments')->willReturn(new ArrayCollection([$payment]));

        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CANCEL)
            ->willReturn(false)
        ;

        $this->stateMachine->expects($this->never())->method('apply');

        $event = new CompletedEvent($order, new Marking());
        ($this->listener)($event);
    }

    public function testItAppliesTransitionCancelOnPayments(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $payment1 = $this->createMock(PaymentInterface::class);
        $payment2 = $this->createMock(PaymentInterface::class);

        $order->method('getPayments')->willReturn(new ArrayCollection([$payment1, $payment2]));

        $this->stateMachine
            ->method('can')
            ->willReturnCallback(fn ($payment) => in_array($payment, [$payment1, $payment2], true))
        ;

        $this->stateMachine
            ->expects($this->exactly(2))
            ->method('apply')
            ->with(
                $this->logicalOr(
                    $this->identicalTo($payment1),
                    $this->identicalTo($payment2),
                ),
                PaymentTransitions::GRAPH,
                PaymentTransitions::TRANSITION_CANCEL,
            )
        ;

        $event = new CompletedEvent($order, new Marking());
        ($this->listener)($event);
    }
}
