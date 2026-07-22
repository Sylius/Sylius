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
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\WithoutOwnAdjustmentsPromotionEligibilityChecker;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class WithoutOwnAdjustmentsPromotionEligibilityCheckerTest extends TestCase
{
    private MockObject&PromotionEligibilityCheckerInterface $promotionEligibilityChecker;

    private MockObject&PromotionApplicatorInterface $promotionApplicator;

    private WithoutOwnAdjustmentsPromotionEligibilityChecker $checker;

    private MockObject&PromotionSubjectInterface $subject;

    private MockObject&PromotionInterface $promotion;

    protected function setUp(): void
    {
        $this->promotionEligibilityChecker = $this->createMock(PromotionEligibilityCheckerInterface::class);
        $this->promotionApplicator = $this->createMock(PromotionApplicatorInterface::class);
        $this->checker = new WithoutOwnAdjustmentsPromotionEligibilityChecker(
            $this->promotionEligibilityChecker,
            $this->promotionApplicator,
        );
        $this->subject = $this->createMock(PromotionSubjectInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
    }

    public function testImplementsPromotionEligibilityCheckerInterface(): void
    {
        self::assertInstanceOf(PromotionEligibilityCheckerInterface::class, $this->checker);
    }

    public function testChecksEligibilityWithoutTouchingTheSubjectWhenPromotionIsNotApplied(): void
    {
        $this->subject->method('hasPromotion')->with($this->promotion)->willReturn(false);

        $this->promotionApplicator->expects(self::never())->method('revert');
        $this->promotionApplicator->expects(self::never())->method('apply');

        $this->promotionEligibilityChecker->expects(self::once())
            ->method('isEligible')
            ->with($this->subject, $this->promotion)
            ->willReturn(true);

        self::assertTrue($this->checker->isEligible($this->subject, $this->promotion));
    }

    public function testRevertsItsOwnAppliedPromotionBeforeCheckingAndReappliesItWhenStillEligible(): void
    {
        $this->subject->method('hasPromotion')->with($this->promotion)->willReturn(true);

        $this->promotionApplicator->expects(self::once())->method('revert')->with($this->subject, $this->promotion);

        $this->promotionEligibilityChecker->expects(self::once())
            ->method('isEligible')
            ->with($this->subject, $this->promotion)
            ->willReturn(true);

        $this->promotionApplicator->expects(self::once())->method('apply')->with($this->subject, $this->promotion);

        self::assertTrue($this->checker->isEligible($this->subject, $this->promotion));
    }

    public function testRestoresItsOwnAppliedPromotionAfterCheckingEvenWhenNoLongerEligible(): void
    {
        $this->subject->method('hasPromotion')->with($this->promotion)->willReturn(true);

        $this->promotionApplicator->expects(self::once())->method('revert')->with($this->subject, $this->promotion);

        $this->promotionEligibilityChecker->expects(self::once())
            ->method('isEligible')
            ->with($this->subject, $this->promotion)
            ->willReturn(false);

        $this->promotionApplicator->expects(self::once())->method('apply')->with($this->subject, $this->promotion);

        self::assertFalse($this->checker->isEligible($this->subject, $this->promotion));
    }

    public function testRestoresItsOwnAppliedPromotionWhenTheInnerCheckThrows(): void
    {
        $this->subject->method('hasPromotion')->with($this->promotion)->willReturn(true);

        $this->promotionApplicator->expects(self::once())->method('revert')->with($this->subject, $this->promotion);

        $exception = new \RuntimeException('Eligibility check failed.');
        $this->promotionEligibilityChecker->expects(self::once())
            ->method('isEligible')
            ->with($this->subject, $this->promotion)
            ->willThrowException($exception);

        $this->promotionApplicator->expects(self::once())->method('apply')->with($this->subject, $this->promotion);

        $this->expectExceptionObject($exception);

        $this->checker->isEligible($this->subject, $this->promotion);
    }
}
