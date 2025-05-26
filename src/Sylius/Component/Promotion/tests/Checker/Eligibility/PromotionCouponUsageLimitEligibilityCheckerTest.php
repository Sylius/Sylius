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
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponUsageLimitEligibilityChecker;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class PromotionCouponUsageLimitEligibilityCheckerTest extends TestCase
{
    private MockObject&PromotionSubjectInterface $promotionSubject;

    private MockObject&PromotionCouponInterface $promotionCoupon;

    private PromotionCouponUsageLimitEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->promotionSubject = $this->createMock(PromotionSubjectInterface::class);
        $this->promotionCoupon = $this->createMock(PromotionCouponInterface::class);
        $this->checker = new PromotionCouponUsageLimitEligibilityChecker();
    }

    public function testShouldImplementPromotionCouponEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(PromotionCouponEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldReturnTrueIfUsageLimitIsNotDefined(): void
    {
        $this->promotionCoupon->expects($this->once())->method('getUsageLimit')->willReturn(null);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldReturnTrueIfUsageLimitHasNotBeenReachedYet(): void
    {
        $this->promotionCoupon->expects($this->once())->method('getUsageLimit')->willReturn(42);
        $this->promotionCoupon->expects($this->once())->method('getUsed')->willReturn(41);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldReturnFalseIfUsageLimitHasBeenReached(): void
    {
        $this->promotionCoupon->expects($this->once())->method('getUsageLimit')->willReturn(42);
        $this->promotionCoupon->expects($this->once())->method('getUsed')->willReturn(42);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }
}
