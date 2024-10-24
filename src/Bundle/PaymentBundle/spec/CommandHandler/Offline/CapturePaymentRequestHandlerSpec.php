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

namespace spec\Sylius\Bundle\PaymentBundle\CommandHandler\Offline;

use PhpSpec\ObjectBehavior;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Command\Offline\CapturePaymentRequest;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;

final class CapturePaymentRequestHandlerSpec extends ObjectBehavior
{
    function let(
        PaymentRequestProviderInterface $paymentRequestProvider,
        StateMachineInterface $stateMachine,
    ): void {
        $this->beConstructedWith($paymentRequestProvider, $stateMachine);
    }

    function it_processes_offline_capture(
        PaymentRequestProviderInterface $paymentRequestProvider,
        StateMachineInterface $stateMachine,
        PaymentRequestInterface $paymentRequest,
    ): void {
        $capturePaymentRequest = new CapturePaymentRequest('hash');
        $paymentRequestProvider->provide($capturePaymentRequest)->willReturn($paymentRequest);
        $stateMachine->apply($paymentRequest, PaymentRequestTransitions::GRAPH, PaymentRequestTransitions::TRANSITION_COMPLETE)->shouldBeCalled();

        $this->__invoke($capturePaymentRequest);
    }
}
