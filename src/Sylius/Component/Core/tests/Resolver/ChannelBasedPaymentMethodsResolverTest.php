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

namespace Tests\Sylius\Component\Core\Resolver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Repository\PaymentMethodRepositoryInterface;
use Sylius\Component\Core\Resolver\ChannelBasedPaymentMethodsResolver;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;

final class ChannelBasedPaymentMethodsResolverTest extends TestCase
{
    private MockObject&PaymentMethodRepositoryInterface $paymentMethodRepository;

    private MockObject&PaymentInterface $payment;

    private MockObject&OrderInterface $order;

    private ChannelInterface&MockObject $channel;

    private ChannelBasedPaymentMethodsResolver $resolver;

    protected function setUp(): void
    {
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->payment = $this->createMock(PaymentInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->resolver = new ChannelBasedPaymentMethodsResolver($this->paymentMethodRepository);
    }

    public function testShouldImplementPaymentMethodsResolverInterface(): void
    {
        $this->assertInstanceOf(PaymentMethodsResolverInterface::class, $this->resolver);
    }

    public function testShouldReturnPaymentMethodsMatchedForOrderChannel(): void
    {
        $firstPaymentMethod = $this->createMock(PaymentMethodInterface::class);
        $secondPaymentMethod = $this->createMock(PaymentMethodInterface::class);
        $this->payment->expects($this->exactly(3))->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->paymentMethodRepository
            ->expects($this->once())
            ->method('findEnabledForChannel')
            ->with($this->channel)
            ->willReturn([$firstPaymentMethod, $secondPaymentMethod]);

        $this->assertEquals(
            [$firstPaymentMethod, $secondPaymentMethod],
            $this->resolver->getSupportedMethods($this->payment),
        );
    }

    public function testShouldReturnEmptyCollectionIfThereIsNoEnabledPaymentMethodsForOrderChannel(): void
    {
        $this->payment->expects($this->exactly(3))->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->exactly(2))->method('getChannel')->willReturn($this->channel);
        $this->paymentMethodRepository
            ->expects($this->once())
            ->method('findEnabledForChannel')
            ->with($this->channel)
            ->willReturn([]);

        $this->assertEquals([], $this->resolver->getSupportedMethods($this->payment));
    }

    public function testShouldSupportShipmentsWithOrderAndItsShippingAddressDefined(): void
    {
        $this->payment->expects($this->exactly(2))->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);

        $this->assertTrue($this->resolver->supports($this->payment));
    }

    public function testShouldNotSupportPaymentsForOrderWithNotAssignedChannel(): void
    {
        $this->payment->expects($this->exactly(2))->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getChannel')->willReturn(null);

        $this->assertFalse($this->resolver->supports($this->payment));
    }

    public function testShouldNotSupportPaymentIfPaymentIsNotInstanceOfCorePaymentInterface(): void
    {
        $this->assertFalse($this->resolver->supports($this->createMock(BasePaymentInterface::class)));
    }

    public function testShouldNotSupportPaymentsWhichHasNoOrderDefined(): void
    {
        $this->payment->expects($this->once())->method('getOrder')->willReturn(null);

        $this->assertFalse($this->resolver->supports($this->payment));
    }
}
