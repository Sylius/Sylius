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

namespace Tests\Sylius\Component\Core\Payment;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Payment\IdBasedInvoiceNumberGenerator;
use Sylius\Component\Core\Payment\InvoiceNumberGeneratorInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Payment\Model\PaymentInterface;

final class IdBasedInvoiceNumberGeneratorTest extends TestCase
{
    private IdBasedInvoiceNumberGenerator $generator;

    protected function setUp(): void
    {
        $this->generator = new IdBasedInvoiceNumberGenerator();
    }

    public function testShouldImplementInvoiceNumberGeneratorInterface(): void
    {
        $this->assertInstanceOf(InvoiceNumberGeneratorInterface::class, $this->generator);
    }

    public function testShouldGenerateInvoiceNumberBasedOn(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $payment = $this->createMock(PaymentInterface::class);
        $order->expects($this->once())->method('getId')->willReturn('001');
        $payment->expects($this->once())->method('getId')->willReturn('1');

        $this->assertSame('001-1', $this->generator->generate($order, $payment));
    }
}
