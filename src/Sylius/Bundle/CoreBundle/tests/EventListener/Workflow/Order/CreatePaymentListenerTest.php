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
use Sylius\Bundle\CoreBundle\EventListener\Workflow\Order\CreatePaymentListener;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;
use Symfony\Component\Workflow\Event\CompletedEvent;
use Symfony\Component\Workflow\Marking;

final class CreatePaymentListenerTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private CreatePaymentListener $listener;

    protected function setUp(): void
    {
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->listener = new CreatePaymentListener($this->stateMachine);
    }

    public function testItThrowsAnExceptionOnNonSupportedSubject(): void
    {
        $invalidSubject = new \stdClass();
        $event = new CompletedEvent($invalidSubject, new Marking());

        $this->expectException(\InvalidArgumentException::class);

        ($this->listener)($event);
    }

    public function testItDoesNothingIfPaymentCannotBeCreated(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $payment = $this->createMock(PaymentInterface::class);

        $order->method('getPayments')->willReturn(new ArrayCollection([$payment]));

        $this->stateMachine
            ->expects($this->once())
            ->method('can')
            ->with($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_CREATE)
            ->willReturn(false)
        ;

        $this->stateMachine->expects($this->never())->method('apply');

        $event = new CompletedEvent($order, new Marking());
        ($this->listener)($event);
    }

    public function testItAppliesTransitionCreateOnPayments(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $payment1 = $this->createMock(PaymentInterface::class);
        $payment2 = $this->createMock(PaymentInterface::class);

        $order->method('getPayments')->willReturn(new ArrayCollection([$payment1, $payment2]));

        $this->stateMachine
            ->method('can')
            ->willReturnCallback(fn ($payment, $graph, $transition) => in_array($payment, [$payment1, $payment2], true))
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
                PaymentTransitions::TRANSITION_CREATE,
            )
        ;

        $event = new CompletedEvent($order, new Marking());
        ($this->listener)($event);
    }
}
