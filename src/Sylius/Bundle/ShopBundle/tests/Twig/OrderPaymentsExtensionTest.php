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

namespace Tests\Sylius\Bundle\ShopBundle\Twig;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ShopBundle\Twig\OrderPaymentsExtension;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;
use Twig\Extension\AbstractExtension;

final class OrderPaymentsExtensionTest extends TestCase
{
    private MockObject&PaymentMethodsResolverInterface $paymentMethodsResolver;

    private OrderPaymentsExtension $orderPaymentsExtension;

    protected function setUp(): void
    {
        $this->paymentMethodsResolver = $this->createMock(PaymentMethodsResolverInterface::class);

        $this->orderPaymentsExtension = new OrderPaymentsExtension($this->paymentMethodsResolver);
    }

    public function testTwigExtension(): void
    {
        $this->assertInstanceOf(AbstractExtension::class, $this->orderPaymentsExtension);
    }

    public function testReturnsFalseIfOrderHasNoNewPayments(): void
    {
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);

        $order->expects($this->once())->method('getPayments')->willReturn(new ArrayCollection());

        $this->assertFalse($this->orderPaymentsExtension->allNewPaymentsCanBePaid($order));
    }

    public function testReturnsFalseWhenAllNewPaymentsHaveNoSupportedMethods(): void
    {
        /** @var PaymentInterface&MockObject $firstPayment */
        $firstPayment = $this->createMock(PaymentInterface::class);
        /** @var PaymentInterface&MockObject $secondPayment */
        $secondPayment = $this->createMock(PaymentInterface::class);
        /** @var PaymentInterface&MockObject $thirdPayment */
        $thirdPayment = $this->createMock(PaymentInterface::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);

        $firstPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $secondPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_CANCELLED);
        $thirdPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $order->expects($this->once())->method('getPayments')->willReturn(new ArrayCollection([
            $firstPayment,
            $secondPayment,
            $thirdPayment,
        ]));

        $this->paymentMethodsResolver
            ->method('getSupportedMethods')
            ->willReturnMap([
                [$firstPayment, []],
                [$thirdPayment, []],
            ])
        ;

        $this->assertFalse($this->orderPaymentsExtension->allNewPaymentsCanBePaid($order));
    }

    public function testReturnsFalseWhenAtLeastOneNewPaymentHasNoSupportedMethods(): void
    {
        /** @var PaymentInterface&MockObject $firstPayment */
        $firstPayment = $this->createMock(PaymentInterface::class);
        /** @var PaymentInterface&MockObject $secondPayment */
        $secondPayment = $this->createMock(PaymentInterface::class);
        /** @var PaymentInterface&MockObject $thirdPayment */
        $thirdPayment = $this->createMock(PaymentInterface::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);

        $firstPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $secondPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_CANCELLED);
        $thirdPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $order->expects($this->once())->method('getPayments')->willReturn(new ArrayCollection([
            $firstPayment,
            $secondPayment,
            $thirdPayment,
        ]));

        $this->paymentMethodsResolver
            ->method('getSupportedMethods')
            ->willReturnMap([
                [$firstPayment, ['method']],
                [$thirdPayment, []],
            ])
        ;

        $this->assertFalse($this->orderPaymentsExtension->allNewPaymentsCanBePaid($order));
    }

    public function testReturnsTrueWhenAllNewPaymentsHaveAtLeastOneSupportedMethod(): void
    {
        /** @var PaymentInterface&MockObject $firstPayment */
        $firstPayment = $this->createMock(PaymentInterface::class);
        /** @var PaymentInterface&MockObject $secondPayment */
        $secondPayment = $this->createMock(PaymentInterface::class);
        /** @var PaymentInterface&MockObject $thirdPayment */
        $thirdPayment = $this->createMock(PaymentInterface::class);
        /** @var OrderInterface&MockObject $order */
        $order = $this->createMock(OrderInterface::class);

        $firstPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $secondPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_CANCELLED);
        $thirdPayment->expects($this->once())->method('getState')->willReturn(PaymentInterface::STATE_NEW);
        $order->expects($this->once())->method('getPayments')->willReturn(new ArrayCollection([
            $firstPayment,
            $secondPayment,
            $thirdPayment,
        ]));

        $this->paymentMethodsResolver
            ->method('getSupportedMethods')
            ->willReturnMap([
                [$firstPayment, ['method', 'another_method']],
                [$thirdPayment, ['method']],
            ])
        ;
        $this->assertTrue($this->orderPaymentsExtension->allNewPaymentsCanBePaid($order));
    }
}
