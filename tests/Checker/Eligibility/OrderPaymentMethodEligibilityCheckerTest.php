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

namespace Sylius\Tests\Checker\Eligibility;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Context\ChannelContextInterface;
use Sylius\Component\Channel\Context\ChannelNotFoundException;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\PaymentMethodInterface;
use Sylius\Component\Core\Payment\Checker\OrderPaymentMethodEligibilityChecker;

final class OrderPaymentMethodEligibilityCheckerTest extends TestCase
{
    /** @var ChannelContextInterface&MockObject */
    private ChannelContextInterface $channelContext;

    private OrderPaymentMethodEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->channelContext = $this->createMock(ChannelContextInterface::class);
        $this->checker = new OrderPaymentMethodEligibilityChecker($this->channelContext);
    }

    public function test_it_returns_false_when_channel_is_not_found(): void
    {
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);

        $this->channelContext
            ->method('getChannel')
            ->willThrowException(new ChannelNotFoundException());

        self::assertFalse($this->checker->isEligible($paymentMethod));
    }

    public function test_it_returns_true_when_payment_method_is_enabled_and_assigned_to_channel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);

        $this->channelContext
            ->method('getChannel')
            ->willReturn($channel);

        $paymentMethod
            ->method('isEnabled')
            ->willReturn(true);

        $paymentMethod
            ->method('hasChannel')
            ->with($channel)
            ->willReturn(true);

        self::assertTrue($this->checker->isEligible($paymentMethod));
    }

    public function test_it_returns_false_when_payment_method_is_disabled(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);

        $this->channelContext
            ->method('getChannel')
            ->willReturn($channel);

        $paymentMethod
            ->method('isEnabled')
            ->willReturn(false);

        $paymentMethod
            ->method('hasChannel')
            ->with($channel)
            ->willReturn(true);

        self::assertFalse($this->checker->isEligible($paymentMethod));
    }

    public function test_it_returns_false_when_payment_method_is_not_assigned_to_channel(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $paymentMethod = $this->createMock(PaymentMethodInterface::class);

        $this->channelContext
            ->method('getChannel')
            ->willReturn($channel);

        $paymentMethod
            ->method('isEnabled')
            ->willReturn(true);

        $paymentMethod
            ->method('hasChannel')
            ->with($channel)
            ->willReturn(false);

        self::assertFalse($this->checker->isEligible($paymentMethod));
    }
}
