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
use Sylius\Component\Promotion\Checker\Eligibility\CompositePromotionEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class CompositePromotionEligibilityCheckerTest extends TestCase
{
    private MockObject&PromotionEligibilityCheckerInterface $firstPromotionEligibilityChecker;

    private MockObject&PromotionEligibilityCheckerInterface $secondPromotionEligibilityChecker;

    private MockObject&PromotionSubjectInterface $promotionSubject;

    private MockObject&PromotionInterface $promotion;

    private CompositePromotionEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->firstPromotionEligibilityChecker = $this->createMock(PromotionEligibilityCheckerInterface::class);
        $this->secondPromotionEligibilityChecker = $this->createMock(PromotionEligibilityCheckerInterface::class);
        $this->promotionSubject = $this->createMock(PromotionSubjectInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->checker = new CompositePromotionEligibilityChecker([
            $this->firstPromotionEligibilityChecker,
            $this->secondPromotionEligibilityChecker,
        ]);
    }

    public function testShouldImplementPromotionEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(PromotionEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldReturnTrueIfAllEligibilityCheckerReturnsTrue(): void
    {
        $this->firstPromotionEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotion)
            ->willReturn(true);
        $this->secondPromotionEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotion)
            ->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldReturnFalseIfAnyEligibilityCheckerReturnsFalse(): void
    {
        $this->firstPromotionEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotion)
            ->willReturn(true);
        $this->secondPromotionEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotion)
            ->willReturn(false);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldStopCheckingAtTheFirstFailingEligibilityChecker(): void
    {
        $this->firstPromotionEligibilityChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotion)
            ->willReturn(false);
        $this->secondPromotionEligibilityChecker
            ->expects($this->never())
            ->method('isEligible')
            ->with($this->promotionSubject, $this->promotion);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldThrowExceptionIfNoEligibilityCheckersArePassed(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        new CompositePromotionEligibilityChecker([]);
    }

    public function testShouldThrowExceptionIfPassedArrayHasNotOnlyEligibilityCheckers(): void
    {
        /** @var PromotionEligibilityCheckerInterface[] $eligibilityCheckers */
        $eligibilityCheckers = [$this->firstPromotionEligibilityChecker, new \stdClass()];
        $this->expectException(\InvalidArgumentException::class);

        new CompositePromotionEligibilityChecker($eligibilityCheckers);
    }
}
