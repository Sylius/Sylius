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

use PHPUnit\Framework\TestCase;
use Sylius\Component\Payment\Model\Payment;
use Sylius\Component\Payment\Model\PaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;

final class PaymentTest extends TestCase
{
    private Payment $payment;

    protected function setUp(): void
    {
        $this->payment = new Payment();
    }

    public function testImplementsSyliusPaymentInterface(): void
    {
        $this->assertInstanceOf(PaymentInterface::class, $this->payment);
    }

    public function testHasNoIdByDefault(): void
    {
        $this->assertNull($this->payment->getId());
    }

    public function testHasNoPaymentMethodByDefault(): void
    {
        $this->assertNull($this->payment->getMethod());
    }

    public function testItsPaymentMethodIsMutable(): void
    {
        $methodMock = $this->createMock(PaymentMethodInterface::class);

        $this->payment->setMethod($methodMock);

        $this->assertSame($methodMock, $this->payment->getMethod());
    }

    public function testHasNoCurrencyCodeByDefault(): void
    {
        $this->assertNull($this->payment->getCurrencyCode());
    }

    public function testItsCurrencyCodeIsMutable(): void
    {
        $this->payment->setCurrencyCode('EUR');
        $this->assertSame('EUR', $this->payment->getCurrencyCode());
    }

    public function testHasAmountEqualTo0ByDefault(): void
    {
        $this->assertSame(0, $this->payment->getAmount());
    }

    public function testItsAmountIsMutable(): void
    {
        $this->payment->setAmount(4999);
        $this->assertSame(4999, $this->payment->getAmount());
    }

    public function testHasCartStateByDefault(): void
    {
        $this->assertSame(PaymentInterface::STATE_CART, $this->payment->getState());
    }

    public function testItsStateIsMutable(): void
    {
        $this->payment->setState(PaymentInterface::STATE_COMPLETED);
        $this->assertSame(PaymentInterface::STATE_COMPLETED, $this->payment->getState());
    }

    public function testItsCreationDateIsMutable(): void
    {
        $date = new \DateTime('last year');

        $this->payment->setCreatedAt($date);
        $this->assertSame($date, $this->payment->getCreatedAt());
    }

    public function testHasNoLastUpdateDateByDefault(): void
    {
        $this->assertNull($this->payment->getUpdatedAt());
    }

    public function testItsLastUpdateDateIsMutable(): void
    {
        $date = new \DateTime('last year');

        $this->payment->setUpdatedAt($date);
        $this->assertSame($date, $this->payment->getUpdatedAt());
    }

    public function testItsDetailsAreMutable(): void
    {
        $this->payment->setDetails(['foo', 'bar']);
        $this->assertSame(['foo', 'bar'], $this->payment->getDetails());
    }
}
