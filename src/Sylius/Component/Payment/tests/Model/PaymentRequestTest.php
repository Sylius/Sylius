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

namespace Tests\Sylius\Component\Payment\Model;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Model\PaymentRequest;
use Sylius\Component\Payment\Model\PaymentRequestInterface;

final class PaymentRequestTest extends TestCase
{
    /** @var PaymentInterface&MockObject */
    private MockObject $payment;

    /** @var PaymentMethodInterface&MockObject */
    private MockObject $method;

    private PaymentRequest $paymentRequest;

    protected function setUp(): void
    {
        $this->payment = $this->createMock(PaymentInterface::class);
        $this->method = $this->createMock(PaymentMethodInterface::class);
        $this->paymentRequest = new PaymentRequest($this->payment, $this->method);
    }

    public function testImplementsSyliusPaymentRequestInterface(): void
    {
        $this->assertInstanceOf(PaymentRequestInterface::class, $this->paymentRequest);
    }

    public function testHasNoHashByDefault(): void
    {
        $this->assertNull($this->paymentRequest->getId());
    }

    public function testHasAPaymentByDefault(): void
    {
        $this->assertInstanceOf(PaymentInterface::class, $this->paymentRequest->getPayment());
    }

    public function testHasAPaymentMethodByDefault(): void
    {
        $this->assertInstanceOf(PaymentMethodInterface::class, $this->paymentRequest->getMethod());
    }

    public function testItsPaymentMethodIsMutable(): void
    {
        $this->paymentRequest->setMethod($this->method);
        $this->assertSame($this->method, $this->paymentRequest->getMethod());
    }

    public function testItsPaymentIsMutable(): void
    {
        $this->paymentRequest->setPayment($this->payment);
        $this->assertSame($this->payment, $this->paymentRequest->getPayment());
    }

    public function testHasNewStateByDefault(): void
    {
        $this->assertSame(PaymentRequestInterface::STATE_NEW, $this->paymentRequest->getState());
    }

    public function testItsStateIsMutable(): void
    {
        $this->paymentRequest->setState('test_state');
        $this->assertSame('test_state', $this->paymentRequest->getState());
    }

    public function testHasCaptureActionByDefault(): void
    {
        $this->assertSame(PaymentRequestInterface::ACTION_CAPTURE, $this->paymentRequest->getAction());
    }

    public function testItsActionIsMutable(): void
    {
        $this->paymentRequest->setAction('test_action');
        $this->assertSame('test_action', $this->paymentRequest->getAction());
    }

    public function testHasNullPayloadByDefault(): void
    {
        $this->assertNull($this->paymentRequest->getPayload());
    }

    public function testItsPayloadIsMutable(): void
    {
        $stdClass = new \stdClass();
        $this->paymentRequest->setPayload($stdClass);
        $this->assertSame($stdClass, $this->paymentRequest->getPayload());
    }

    public function testHasEmptyArrayResponseDataByDefault(): void
    {
        $this->assertSame([], $this->paymentRequest->getResponseData());
    }

    public function testItsResponseDataAreMutable(): void
    {
        $this->paymentRequest->setResponseData([
            'foo' => 'bar',
            'bar' => 'foo',
        ]);
        $this->assertSame(
            ['foo' => 'bar', 'bar' => 'foo'],
            $this->paymentRequest->getResponseData(),
        );
    }

    public function testItsCreationDateIsMutable(): void
    {
        $date = new \DateTime('last year');

        $this->paymentRequest->setCreatedAt($date);
        $this->assertSame($date, $this->paymentRequest->getCreatedAt());
    }

    public function testHasNoLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->paymentRequest->getUpdatedAt());
    }

    public function testItsLastUpdateDateIsMutable(): void
    {
        $date = new \DateTime('last year');

        $this->paymentRequest->setUpdatedAt($date);
        $this->assertSame($date, $this->paymentRequest->getUpdatedAt());
    }
}
