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

namespace Tests\Sylius\Component\Core\Checker\Eligibility;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Checker\Eligibility\PromotionCouponChannelEligibilityChecker;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\PromotionInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\Promotion;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class PromotionCouponChannelEligibilityCheckerTest extends TestCase
{
    private MockObject&OrderInterface $promotionSubject;

    private MockObject&PromotionCouponInterface $promotionCoupon;

    private ChannelInterface&MockObject $channel;

    private MockObject&PromotionInterface $promotion;

    private PromotionCouponChannelEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->promotionSubject = $this->createMock(OrderInterface::class);
        $this->promotionCoupon = $this->createMock(PromotionCouponInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->checker = new PromotionCouponChannelEligibilityChecker();
    }

    public function testShouldImplementPromotionCouponEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(PromotionCouponEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldReturnTrueIfPromotionCouponIsEnabledInChannel(): void
    {
        $this->promotionSubject->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->promotionCoupon->expects($this->once())->method('getPromotion')->willReturn($this->promotion);
        $this->promotion->expects($this->once())->method('hasChannel')->with($this->channel)->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldReturnFalseIfPromotionCouponIsNotEnabledInChannel(): void
    {
        $this->promotionSubject->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->promotionCoupon->expects($this->once())->method('getPromotion')->willReturn($this->promotion);
        $this->promotion->expects($this->once())->method('hasChannel')->with($this->channel)->willReturn(false);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldThrowInvalidArgumentExceptionWhenWrongPromotionSubjectProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->checker->isEligible(
            $this->createMock(PromotionSubjectInterface::class),
            $this->promotionCoupon,
        );
    }

    public function testShouldThrowInvalidArgumentExceptionWhenDifferentParameterThanPromotionInterfaceProvided(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->promotionSubject->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->promotionCoupon->expects($this->once())->method('getPromotion')->willReturn($this->createMock(Promotion::class));

        $this->checker->isEligible($this->promotionSubject, $this->promotionCoupon);
    }

    public function testShouldThrowInvalidArgumentExceptionWhenOrderChannelIsNull(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->promotionSubject->expects($this->once())->method('getChannel')->willReturn(null);

        $this->checker->isEligible($this->promotionSubject, $this->promotionCoupon);
    }
}
