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

namespace Tests\Sylius\Bundle\PaymentBundle\CommandHandler\Offline;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\PaymentBundle\Command\Offline\CapturePaymentRequest;
use Sylius\Bundle\PaymentBundle\CommandHandler\Offline\CapturePaymentRequestHandler;
use Sylius\Bundle\PaymentBundle\Provider\PaymentRequestProviderInterface;
use Sylius\Component\Payment\Model\PaymentRequestInterface;
use Sylius\Component\Payment\PaymentRequestTransitions;

final class CapturePaymentRequestHandlerTest extends TestCase
{
    private MockObject&PaymentRequestProviderInterface $paymentRequestProvider;

    private MockObject&StateMachineInterface $stateMachine;

    private CapturePaymentRequestHandler $capturePaymentRequestHandler;

    private MockObject&PaymentRequestInterface $paymentRequest;

    protected function setUp(): void
    {
        $this->paymentRequestProvider = $this->createMock(PaymentRequestProviderInterface::class);
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->capturePaymentRequestHandler = new CapturePaymentRequestHandler(
            $this->paymentRequestProvider,
            $this->stateMachine,
        );
        $this->paymentRequest = $this->createMock(PaymentRequestInterface::class);
    }

    public function testProcessesOfflineCapture(): void
    {
        $capturePaymentRequest = new CapturePaymentRequest('hash');
        $this->paymentRequestProvider->expects(self::once())
            ->method('provide')->with($capturePaymentRequest)
            ->willReturn($this->paymentRequest);

        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with(
                $this->paymentRequest,
                PaymentRequestTransitions::GRAPH,
                PaymentRequestTransitions::TRANSITION_COMPLETE,
            );

        $this->capturePaymentRequestHandler->__invoke($capturePaymentRequest);
    }
}
