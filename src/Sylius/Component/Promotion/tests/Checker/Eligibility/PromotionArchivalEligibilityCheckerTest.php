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
use Sylius\Component\Promotion\Checker\Eligibility\PromotionArchivalEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class PromotionArchivalEligibilityCheckerTest extends TestCase
{
    private MockObject&PromotionSubjectInterface $promotionSubject;

    private MockObject&PromotionInterface $promotion;

    private PromotionArchivalEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->promotionSubject = $this->createMock(PromotionSubjectInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->checker = new PromotionArchivalEligibilityChecker();
    }

    public function testShouldImplementPromotionEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(PromotionEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldBeEligibleWhenArchivedAtIsNull(): void
    {
        $this->promotion->expects($this->once())->method('getArchivedAt')->willReturn(null);

        $this->assertTrue($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }

    public function testShouldNotBeEligibleWhenArchivedAtIsNotNull(): void
    {
        $this->promotion->expects($this->once())->method('getArchivedAt')->willReturn(new \DateTime());

        $this->assertFalse($this->checker->isEligible($this->promotionSubject, $this->promotion));
    }
}
