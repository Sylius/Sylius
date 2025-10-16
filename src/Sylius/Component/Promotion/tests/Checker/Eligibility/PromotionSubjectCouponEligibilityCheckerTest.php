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

namespace Tests\Sylius\Component\Promotion\Checker\Eligibility;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionSubjectCouponEligibilityChecker;
use Sylius\Component\Promotion\Model\PromotionCouponAwarePromotionSubjectInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;

final class PromotionSubjectCouponEligibilityCheckerTest extends TestCase
{
    private MockObject&PromotionCouponAwarePromotionSubjectInterface $promotionSubject;

    private MockObject&PromotionInterface $promotion;

    private MockObject&PromotionCouponInterface $promotionCoupon;

    private MockObject&PromotionCouponEligibilityCheckerInterface $promotionCouponEligibilityChecker;

    private PromotionSubjectCouponEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->promotionCouponEligibilityChecker = $this->createMock(PromotionCouponEligibilityCheckerInterface::class);
        $this->promotionSubject = $this->createMock(PromotionCouponAwarePromotionSubjectInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->promotionCoupon = $this->createMock(PromotionCouponInterface::class);
        $this->checker = new PromotionSubjectCouponEligibilityChecker($this->promotionCouponEligibilityChecker);
    }

    public function testShouldImplementPromotionEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(PromotionEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldReturnTrueIfSubjectCouponsAreEligibleToPromotion(): void
    {
        $this->promotion->expects($this->once())->method('isCouponBased')->willReturn(true);
        $this->promotionSubject->expects($this->once())->method('getPromotionCoupon')->willReturn($this->promotionCoupon);
        $this->promotionCoupon->expects($this->once())->method('getPromotion')->willReturn($this->promotion);
        $this->promotionCouponEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotionCoupon)
            ->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldReturnFalseIfSubjectIsNotCouponAware(): void
    {
        $this->promotion->expects($this->once())->method('isCouponBased')->willReturn(true);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldReturnFalseIfSubjectHaveNoCoupon(): void
    {
        $this->promotion->expects($this->once())->method('isCouponBased')->willReturn(true);
        $this->promotionSubject->expects($this->once())->method('getPromotionCoupon')->willReturn(null);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldReturnFalseIfSubjectCouponsComeFromAnotherPromotion(): void
    {
        $otherPromotion = $this->createMock(PromotionInterface::class);
        $this->promotion->expects($this->once())->method('isCouponBased')->willReturn(true);
        $this->promotionSubject->expects($this->once())->method('getPromotionCoupon')->willReturn($this->promotionCoupon);
        $this->promotionCoupon->expects($this->once())->method('getPromotion')->willReturn($otherPromotion);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldReturnFalseIfSubjectCouponIsNotEligible(): void
    {
        $this->promotion->expects($this->once())->method('isCouponBased')->willReturn(true);
        $this->promotionSubject->expects($this->once())->method('getPromotionCoupon')->willReturn($this->promotionCoupon);
        $this->promotionCoupon->expects($this->once())->method('getPromotion')->willReturn($this->promotion);
        $this->promotionCouponEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotionCoupon)
            ->willReturn(false);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }
}
