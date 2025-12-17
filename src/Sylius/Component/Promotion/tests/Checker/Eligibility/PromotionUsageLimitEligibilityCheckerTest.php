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
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionUsageLimitEligibilityChecker;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class PromotionUsageLimitEligibilityCheckerTest extends TestCase
{
    private MockObject&PromotionSubjectInterface $promotionSubject;

    private MockObject&PromotionInterface $promotion;

    private PromotionUsageLimitEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->promotionSubject = $this->createMock(PromotionSubjectInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->checker = new PromotionUsageLimitEligibilityChecker();
    }

    public function testShouldImplementPromotionEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(PromotionEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldReturnTrueIfPromotionHaveNoUsageLimit(): void
    {
        $this->promotion->expects($this->once())->method('getUsageLimit')->willReturn(null);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldReturnTrueIfUsageLimitHaveNotBeenExceeded(): void
    {
        $this->promotion->expects($this->once())->method('getUsageLimit')->willReturn(10);
        $this->promotion->expects($this->once())->method('getUsed')->willReturn(5);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldReturnFalseIfUsageLimitHaveBeenExceeded(): void
    {
        $this->promotion->expects($this->once())->method('getUsageLimit')->willReturn(10);
        $this->promotion->expects($this->once())->method('getUsed')->willReturn(15);

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }
}
