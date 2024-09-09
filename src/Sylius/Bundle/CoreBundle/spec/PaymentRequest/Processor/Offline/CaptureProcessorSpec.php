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

namespace spec\Sylius\Bundle\CoreBundle\PaymentRequest\Processor\Offline;

use PhpSpec\ObjectBehavior;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentTransitions;

final class CaptureProcessorSpec extends ObjectBehavior
{
    function let(
        StateMachineInterface $stateMachine
    ): void {
        $this->beConstructedWith($stateMachine);
    }

    function it_processes_offline_capture(
        StateMachineInterface $stateMachine,
        PaymentRequestInterface $paymentRequest,
        PaymentInterface $payment
    ): void {
        $paymentRequest->getPayment()->willReturn($payment);
        $stateMachine->can($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_PROCESS)->willReturn(true);

        $this->process($paymentRequest);

        $stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_PROCESS)->shouldHaveBeenCalledOnce();
        $paymentRequest->setState(PaymentRequestInterface::STATE_COMPLETED)->shouldHaveBeenCalledOnce();
    }

    function it_processes_offline_capture_if_payment_cannot_be_processed(
        StateMachineInterface $stateMachine,
        PaymentRequestInterface $paymentRequest,
        PaymentInterface $payment
    ): void {
        $paymentRequest->getPayment()->willReturn($payment);
        $stateMachine->can($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_PROCESS)->willReturn(false);

        $this->process($paymentRequest);

        $stateMachine->apply($payment, PaymentTransitions::GRAPH, PaymentTransitions::TRANSITION_PROCESS)->shouldNotHaveBeenCalled();
        $paymentRequest->setState(PaymentRequestInterface::STATE_COMPLETED)->shouldHaveBeenCalledOnce();
    }
}
