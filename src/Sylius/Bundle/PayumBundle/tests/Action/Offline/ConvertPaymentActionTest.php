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

namespace Tests\Sylius\Bundle\PayumBundle\Action\Offline;

use Payum\Core\Action\ActionInterface;
use Payum\Core\Model\PaymentInterface;
use Payum\Core\Request\Capture;
use Payum\Core\Request\Convert;
use Payum\Offline\Constants;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\PayumBundle\Action\Offline\ConvertPaymentAction;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class ConvertPaymentActionTest extends TestCase
{
    private ConvertPaymentAction $convertPaymentAction;

    private MockObject&PaymentInterface $payment;

    private Convert&MockObject $request;

    protected function setUp(): void
    {
        parent::setUp();
        $this->convertPaymentAction = new ConvertPaymentAction();
        $this->payment = $this->createMock(PaymentInterface::class);
        $this->request = $this->createMock(Convert::class);
    }

    public function testPayumAction(): void
    {
        self::assertInstanceOf(ActionInterface::class, $this->convertPaymentAction);
    }

    public function testConvertsPaymentToOfflineAction(): void
    {
        $this->request->expects(self::once())->method('getTo')->willReturn('array');

        $this->request->expects(self::once())->method('getSource')->willReturn($this->payment);

        $this->request->expects(self::once())->method('setResult')->with([
            Constants::FIELD_PAID => false,
        ]);

        $this->convertPaymentAction->execute($this->request);
    }

    public function testSupportsOnlyConvertRequest(): void
    {
        /** @var Capture&MockObject $captureRequest */
        $captureRequest = $this->createMock(Capture::class);

        $this->request->expects(self::once())->method('getTo')->willReturn('array');

        $this->request->expects(self::once())->method('getSource')->willReturn($this->payment);

        self::assertTrue($this->convertPaymentAction->supports($this->request));
        self::assertFalse($this->convertPaymentAction->supports($captureRequest));
    }

    public function testSupportsOnlyConvertingToArrayFromPayment(): void
    {
        /** @var Convert&MockObject $fromSomethingElseToSomethingElseRequest */
        $fromSomethingElseToSomethingElseRequest = $this->createMock(Convert::class);
        /** @var Convert&MockObject $fromPaymentToArrayRequest */
        $fromPaymentToArrayRequest = $this->createMock(Convert::class);
        /** @var PaymentMethodInterface&MockObject $method */
        $method = $this->createMock(PaymentMethodInterface::class);

        $fromPaymentToArrayRequest
            ->method('getTo')
            ->willReturn('array');
        $fromPaymentToArrayRequest
            ->method('getSource')
            ->willReturn($this->payment);

        $fromSomethingElseToSomethingElseRequest
            ->method('getTo')
            ->willReturn('json');
        $fromSomethingElseToSomethingElseRequest
            ->method('getSource')
            ->willReturn($method);

        self::assertTrue($this->convertPaymentAction->supports($fromPaymentToArrayRequest));
        self::assertFalse($this->convertPaymentAction->supports($fromSomethingElseToSomethingElseRequest));
    }
}
