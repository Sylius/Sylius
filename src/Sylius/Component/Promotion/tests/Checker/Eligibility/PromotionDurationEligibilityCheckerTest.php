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
use Sylius\Component\Promotion\Checker\Eligibility\PromotionDurationEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class PromotionDurationEligibilityCheckerTest extends TestCase
{
    private MockObject&PromotionSubjectInterface $promotionSubject;

    private MockObject&PromotionInterface $promotion;

    private PromotionDurationEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->promotionSubject = $this->createMock(PromotionSubjectInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->checker = new PromotionDurationEligibilityChecker();
    }

    public function testShouldImplementPromotionEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(PromotionEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldReturnFalseIfPromotionHasNotStartedYet(): void
    {
        $this->promotion->expects($this->once())->method('getStartsAt')->willReturn(new \DateTime('+3 days'));

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldReturnFalseIfPromotionHasAlreadyEnded(): void
    {
        $this->promotion->expects($this->once())->method('getStartsAt')->willReturn(new \DateTime('-5 days'));
        $this->promotion->expects($this->once())->method('getEndsAt')->willReturn(new \DateTime('-3 days'));

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldReturnTrueIfPromotionIsCurrentlyAvailable(): void
    {
        $this->promotion->expects($this->once())->method('getStartsAt')->willReturn(new \DateTime('-2 days'));
        $this->promotion->expects($this->once())->method('getEndsAt')->willReturn(new \DateTime('+2 days'));

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldReturnTrueIfPromotionDatesAreNotSpecified(): void
    {
        $this->promotion->expects($this->once())->method('getStartsAt')->willReturn(null);
        $this->promotion->expects($this->once())->method('getEndsAt')->willReturn(null);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }
}
