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

namespace Tests\Sylius\Component\Payment\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Payment\Factory\PaymentFactory;
use Sylius\Component\Payment\Factory\PaymentFactoryInterface;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class PaymentFactoryTest extends TestCase
{
    private MockObject $paymentFactoryMock;

    /** @var PaymentFactory<PaymentInterface> */
    private PaymentFactory $paymentFactory;

    protected function setUp(): void
    {
        $this->paymentFactoryMock = $this->createMock(FactoryInterface::class);
        $this->paymentFactory = new PaymentFactory($this->paymentFactoryMock);
    }

    public function testImplementsPaymentFactoryInterface(): void
    {
        $this->assertInstanceOf(PaymentFactoryInterface::class, $this->paymentFactory);
    }

    public function testImplementsFactoryInterface(): void
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->paymentFactory);
    }

    public function testDelegatesCreationOfNewResource(): void
    {
        $paymentMock = $this->createMock(PaymentInterface::class);

        $this->paymentFactoryMock
            ->expects($this->once())
            ->method('createNew')
            ->willReturn($paymentMock);

        $this->assertSame($paymentMock, $this->paymentFactory->createNew());
    }

    public function testCreatesPaymentWithCurrencyAndAmount(): void
    {
        $paymentMock = $this->createMock(PaymentInterface::class);

        $this->paymentFactoryMock
            ->expects($this->once())
            ->method('createNew')
            ->willReturn($paymentMock);
        $paymentMock
            ->expects($this->once())
            ->method('setAmount')
            ->with(1234);
        $paymentMock
            ->expects($this->once())
            ->method('setCurrencyCode')
            ->with('EUR');

        $this->paymentFactory->createWithAmountAndCurrencyCode(1234, 'EUR');
    }
}
