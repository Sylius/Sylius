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
use Sylius\Component\Core\Resolver\DefaultPaymentMethodResolver;
use Sylius\Component\Payment\Exception\UnresolvedDefaultPaymentMethodException;
use Sylius\Component\Payment\Model\PaymentInterface as BasePaymentInterface;
use Sylius\Component\Payment\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\DefaultPaymentMethodResolverInterface;

final class DefaultPaymentMethodResolverTest extends TestCase
{
    private MockObject&PaymentMethodRepositoryInterface $paymentMethodRepository;

    private MockObject&PaymentInterface $payment;

    private ChannelInterface&MockObject $channel;

    private MockObject&OrderInterface $order;

    private DefaultPaymentMethodResolver $resolver;

    protected function setUp(): void
    {
        $this->paymentMethodRepository = $this->createMock(PaymentMethodRepositoryInterface::class);
        $this->payment = $this->createMock(PaymentInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->resolver = new DefaultPaymentMethodResolver($this->paymentMethodRepository);
    }

    public function testShouldImplementPaymentMethodResolverInterface(): void
    {
        $this->assertInstanceOf(DefaultPaymentMethodResolverInterface::class, $this->resolver);
    }

    public function testShouldThrowInvalidArgumentExceptionIfSubjectNotImplementCorePaymentInterface(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->resolver->getDefaultPaymentMethod($this->createMock(BasePaymentInterface::class));
    }

    public function testShouldThrowUnresolvedDefaultPaymentMethodExceptionIfThereIsNoEnabledPaymentMethodsInDatabase(): void
    {
        $this->expectException(UnresolvedDefaultPaymentMethodException::class);
        $this->payment->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->paymentMethodRepository
            ->expects($this->once())
            ->method('findEnabledForChannel')
            ->with($this->channel)
            ->willReturn([]);

        $this->resolver->getDefaultPaymentMethod($this->payment);
    }

    public function testShouldReturnFirstPaymentMethodFromAvailableWhichIsEnclosedInChannel(): void
    {
        $firstPaymentMethod = $this->createMock(PaymentMethodInterface::class);
        $secondPaymentMethod = $this->createMock(PaymentMethodInterface::class);
        $this->payment->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->paymentMethodRepository
            ->expects($this->once())
            ->method('findEnabledForChannel')
            ->with($this->channel)
            ->willReturn([$firstPaymentMethod, $secondPaymentMethod]);

        $this->assertSame($firstPaymentMethod, $this->resolver->getDefaultPaymentMethod($this->payment));
    }
}
