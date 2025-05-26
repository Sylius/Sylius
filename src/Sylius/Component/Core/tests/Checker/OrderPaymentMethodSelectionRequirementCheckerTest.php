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

namespace Tests\Sylius\Component\Core\Checker;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementChecker;
use Sylius\Component\Core\Checker\OrderPaymentMethodSelectionRequirementCheckerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PaymentInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Payment\Resolver\PaymentMethodsResolverInterface;

final class OrderPaymentMethodSelectionRequirementCheckerTest extends TestCase
{
    private MockObject&PaymentMethodsResolverInterface $paymentMethodsResolver;

    private MockObject&OrderInterface $order;

    private ChannelInterface&MockObject $channel;

    private MockObject&PaymentInterface $payment;

    private MockObject&PaymentMethodInterface $paymentMethod;

    private OrderPaymentMethodSelectionRequirementChecker $checker;

    protected function setUp(): void
    {
        $this->paymentMethodsResolver = $this->createMock(PaymentMethodsResolverInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->payment = $this->createMock(PaymentInterface::class);
        $this->paymentMethod = $this->createMock(PaymentMethodInterface::class);
        $this->checker = new OrderPaymentMethodSelectionRequirementChecker($this->paymentMethodsResolver);
    }

    public function testShouldImplementOrderPaymentNecessityCheckerInterface(): void
    {
        $this->assertInstanceOf(OrderPaymentMethodSelectionRequirementCheckerInterface::class, $this->checker);
    }

    public function testShouldSayThatPaymentMethodHasToBeSelectedIfOrderTotalIsBiggerThanZero(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('isSkippingPaymentStepAllowed')->willReturn(false);

        $this->assertTrue($this->checker->isPaymentMethodSelectionRequired($this->order));
    }

    public function testShouldSayThatPaymentMethodDoesNotHaveToBeSelectedIfOrderTotalIsZero(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(0);

        $this->assertFalse($this->checker->isPaymentMethodSelectionRequired($this->order));
    }

    public function testShouldSayThatPaymentMethodHasToBeSelectedIfSkippingPaymentStepIsDisabled(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('isSkippingPaymentStepAllowed')->willReturn(false);

        $this->assertTrue($this->checker->isPaymentMethodSelectionRequired($this->order));
    }

    public function testShouldSayThatPaymentMethodDoesNotHaveToBeSelectedIfSkippingPaymentStepIsEnabledAndThereIsOnlyOnePaymentMethodAvailable(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->exactly(2))->method('getPayments')->willReturn(new ArrayCollection([$this->payment]));
        $this->paymentMethodsResolver
            ->expects($this->once())
            ->method('getSupportedMethods')
            ->with($this->payment)
            ->willReturn([$this->paymentMethod]);
        $this->channel->expects($this->once())->method('isSkippingPaymentStepAllowed')->willReturn(true);

        $this->assertFalse($this->checker->isPaymentMethodSelectionRequired($this->order));
    }

    public function testShouldSayThatPaymentMethodHasToBeSelectedIfSkippingPaymentStepIsEnabledAndThereAreMoreThanOnePaymentMethodsAvailable(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->exactly(2))->method('getPayments')->willReturn(new ArrayCollection([$this->payment]));
        $this->paymentMethodsResolver
            ->expects($this->once())
            ->method('getSupportedMethods')
            ->with($this->payment)
            ->willReturn([$this->paymentMethod, $this->createMock(PaymentMethodInterface::class)]);
        $this->channel->expects($this->once())->method('isSkippingPaymentStepAllowed')->willReturn(true);

        $this->assertTrue($this->checker->isPaymentMethodSelectionRequired($this->order));
    }

    public function testShouldSayThatPaymentMethodHasToBeSelectedIfSkippingPaymentStepIsEnabledButThereAreNoPaymentMethodsAvailable(): void
    {
        $this->order->expects($this->once())->method('getTotal')->willReturn(1000);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->exactly(2))->method('getPayments')->willReturn(new ArrayCollection([$this->payment]));
        $this->paymentMethodsResolver
            ->expects($this->once())
            ->method('getSupportedMethods')
            ->with($this->payment)
            ->willReturn([]);
        $this->channel->expects($this->once())->method('isSkippingPaymentStepAllowed')->willReturn(true);

        $this->assertTrue($this->checker->isPaymentMethodSelectionRequired($this->order));
    }
}
