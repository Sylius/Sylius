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

namespace Tests\Sylius\Bundle\ApiBundle\Applicator;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Applicator\PaymentStateMachineTransitionApplicator;
use Sylius\Bundle\ApiBundle\Exception\StateMachineTransitionFailedException;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class PaymentStateMachineTransitionApplicatorTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private PaymentStateMachineTransitionApplicator $paymentStateMachineTransitionApplicator;

    private MockObject&PaymentInterface $payment;

    public function testCompletesPayment(): void
    {
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with(
                $this->payment,
                PaymentTransitions::GRAPH,
                PaymentTransitions::TRANSITION_COMPLETE,
            )->willReturn(true);

        $this->stateMachine->expects(self::once())
            ->method('apply')->with(
                $this->payment,
                PaymentTransitions::GRAPH,
                PaymentTransitions::TRANSITION_COMPLETE,
            );

        $this->paymentStateMachineTransitionApplicator->complete($this->payment);
    }

    public function testThrowsExceptionIfCannotCompletePayment(): void
    {
        $this->stateMachine->expects(self::once())
            ->method('can')->with(
                $this->payment,
                PaymentTransitions::GRAPH,
                PaymentTransitions::TRANSITION_COMPLETE,
            )
            ->willReturn(false);

        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with(
                $this->payment,
                PaymentTransitions::GRAPH,
                PaymentTransitions::TRANSITION_COMPLETE,
            );

        self::expectException(StateMachineTransitionFailedException::class);

        $this->paymentStateMachineTransitionApplicator->complete($this->payment);
    }

    public function testRefundsPayment(): void
    {
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with(
                $this->payment,
                PaymentTransitions::GRAPH,
                PaymentTransitions::TRANSITION_REFUND,
            )->willReturn(true);

        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with(
                $this->payment,
                PaymentTransitions::GRAPH,
                PaymentTransitions::TRANSITION_REFUND,
            );

        $this->paymentStateMachineTransitionApplicator->refund($this->payment);
    }

    public function testThrowsAnExceptionIfCannotRefundPayment(): void
    {
        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with(
                $this->payment,
                PaymentTransitions::GRAPH,
                PaymentTransitions::TRANSITION_REFUND,
            )->willReturn(false);

        $this->stateMachine->expects(self::never())
            ->method('apply')
            ->with(
                $this->payment,
                PaymentTransitions::GRAPH,
                PaymentTransitions::TRANSITION_REFUND,
            );

        self::expectException(StateMachineTransitionFailedException::class);

        $this->paymentStateMachineTransitionApplicator->refund($this->payment);
    }

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->paymentStateMachineTransitionApplicator = new PaymentStateMachineTransitionApplicator($this->stateMachine);
        $this->payment = $this->createMock(PaymentInterface::class);
    }
}
