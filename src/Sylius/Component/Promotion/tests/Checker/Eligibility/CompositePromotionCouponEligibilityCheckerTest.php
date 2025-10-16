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
use Sylius\Component\Promotion\Checker\Eligibility\CompositePromotionCouponEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionCouponEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionCouponInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class CompositePromotionCouponEligibilityCheckerTest extends TestCase
{
    private MockObject&PromotionCouponEligibilityCheckerInterface $firstPromotionCouponEligibilityChecker;

    private MockObject&PromotionCouponEligibilityCheckerInterface $secondPromotionCouponEligibilityChecker;

    private MockObject&PromotionSubjectInterface $promotionSubject;

    private MockObject&PromotionCouponInterface $promotionCoupon;

    private CompositePromotionCouponEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->firstPromotionCouponEligibilityChecker = $this->createMock(PromotionCouponEligibilityCheckerInterface::class);
        $this->secondPromotionCouponEligibilityChecker = $this->createMock(PromotionCouponEligibilityCheckerInterface::class);
        $this->promotionSubject = $this->createMock(PromotionSubjectInterface::class);
        $this->promotionCoupon = $this->createMock(PromotionCouponInterface::class);
        $this->checker = new CompositePromotionCouponEligibilityChecker([
            $this->firstPromotionCouponEligibilityChecker,
            $this->secondPromotionCouponEligibilityChecker,
        ]);
    }

    public function testShouldImplementPromotionCouponEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(PromotionCouponEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldReturnTrueIfAllEligibilityCheckerReturnsTrue(): void
    {
        $this->firstPromotionCouponEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotionCoupon)
            ->willReturn(true);
        $this->secondPromotionCouponEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotionCoupon)
            ->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldReturnFalseIfAnyEligibilityCheckerReturnsFalse(): void
    {
        $this->firstPromotionCouponEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotionCoupon)
            ->willReturn(true);
        $this->secondPromotionCouponEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotionCoupon)
            ->willReturn(false);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldStopCheckingAtTheFirstFailingEligibilityChecker(): void
    {
        $this->firstPromotionCouponEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotionCoupon)
            ->willReturn(false);
        $this->secondPromotionCouponEligibilityChecker
            ->expects($this->never())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotionCoupon);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotionCoupon));
    }

    public function testShouldThrowExceptionIfNoEligibilityCheckersArePassed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CompositePromotionCouponEligibilityChecker([]);
    }

    public function testShouldThrowExceptionIfPassedArrayHasNotOnlyEligibilityCheckers(): void
    {
        /** @var PromotionCouponEligibilityCheckerInterface[] $eligibilityCheckers */
        $eligibilityCheckers = [
            $this->firstPromotionCouponEligibilityChecker,
            new \stdClass(),
        ];
        $this->expectException(\InvalidArgumentException::class);

        new CompositePromotionCouponEligibilityChecker($eligibilityCheckers);
    }
}
