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

namespace Tests\Sylius\Bundle\PayumBundle\Action;

use Payum\Core\Bridge\Spl\ArrayObject;
use Payum\Core\Exception\RequestNotSupportedException;
use Payum\Core\GatewayInterface;
use Payum\Core\Request\Capture;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PayumBundle\Action\CapturePaymentAction;
use Sylius\Bundle\PayumBundle\Provider\PaymentDescriptionProviderInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;

final class CapturePaymentActionTest extends TestCase
{
    private MockObject&PaymentDescriptionProviderInterface $paymentDescriptionProvider;

    private CapturePaymentAction $capturePaymentAction;

    private Capture&MockObject $capture;

    protected function setUp(): void
    {
        parent::setUp();
        $this->paymentDescriptionProvider = $this->createMock(PaymentDescriptionProviderInterface::class);
        $this->capturePaymentAction = new CapturePaymentAction($this->paymentDescriptionProvider);
        $this->capture = $this->createMock(Capture::class);
    }

    public function testThrowExceptionWhenUnsupportedRequest(): void
    {
        $this->capture->method('getModel')->willReturn(new \stdClass());

        self::expectException(RequestNotSupportedException::class);

        $this->capturePaymentAction->execute($this->capture);
    }

    public function testPerformBasicCapture(): void
    {
        /** @var GatewayInterface&MockObject $gateway */
        $gateway = $this->createMock(GatewayInterface::class);
        /** @var PaymentInterface&MockObject $payment */
        $payment = $this->createMock(PaymentInterface::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);

        $this->capturePaymentAction->setGateway($gateway);

        $payment->expects(self::once())->method('getOrder')->willReturn($order);

        $payment->expects(self::once())->method('getDetails')->willReturn([]);

        $this->capture->expects(self::any())->method('getModel')->willReturn($payment);

        $payment->expects(self::once())->method('setDetails')->with([]);

        $this->capture->expects(self::once())->method('setModel')->with(new ArrayObject());

        $this->capturePaymentAction->execute($this->capture);
    }
}
