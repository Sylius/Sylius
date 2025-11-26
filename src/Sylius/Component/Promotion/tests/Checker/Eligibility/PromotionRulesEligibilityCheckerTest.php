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

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionRulesEligibilityChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

final class PromotionRulesEligibilityCheckerTest extends TestCase
{
    private MockObject&RuleCheckerInterface $firstRuleChecker;

    private MockObject&RuleCheckerInterface $secondRuleChecker;

    private MockObject&PromotionRuleInterface $firstPromotionRule;

    private MockObject&PromotionRuleInterface $secondPromotionRule;

    private MockObject&PromotionInterface $promotion;

    private MockObject&PromotionSubjectInterface $subject;

    private MockObject&ServiceRegistryInterface $serviceRegistry;

    private PromotionRulesEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->firstRuleChecker = $this->createMock(RuleCheckerInterface::class);
        $this->secondRuleChecker = $this->createMock(RuleCheckerInterface::class);
        $this->firstPromotionRule = $this->createMock(PromotionRuleInterface::class);
        $this->secondPromotionRule = $this->createMock(PromotionRuleInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->subject = $this->createMock(PromotionSubjectInterface::class);
        $this->serviceRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->checker = new PromotionRulesEligibilityChecker($this->serviceRegistry);
    }

    public function testShouldImplementPromotionEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(PromotionEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldRecognizeSubjectAsEligibleIfPromotionHasNoRule(): void
    {
        $this->promotion->expects($this->once())->method('hasRules')->willReturn(false);

        $this->assertTrue($this->checker->isEligible($this->subject, $this->promotion));
    }

    public function testShouldRecognizeSubjectAsEligibleIfAllPromotionRulesAreFulfilled(): void
    {
        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->firstPromotionRule, $this->secondPromotionRule]),
        );
        $this->firstPromotionRule->expects($this->once())->method('getType')->willReturn('first_rule');
        $this->firstPromotionRule->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->secondPromotionRule->expects($this->once())->method('getType')->willReturn('second_rule');
        $this->secondPromotionRule->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->serviceRegistry->expects($this->exactly(2))->method('get')->willReturnMap([
            ['first_rule', $this->firstRuleChecker],
            ['second_rule', $this->secondRuleChecker],
        ]);
        $this->firstRuleChecker->expects($this->once())->method('isEligible')->willReturn(true);
        $this->secondRuleChecker->expects($this->once())->method('isEligible')->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->subject, $this->promotion));
    }

    public function testShouldRecognizeSubjectAsNotEligibleIfAnyOfPromotionRuleIsNotFulfilled(): void
    {
        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->firstPromotionRule, $this->secondPromotionRule]),
        );
        $this->firstPromotionRule->expects($this->once())->method('getType')->willReturn('first_rule');
        $this->firstPromotionRule->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->secondPromotionRule->expects($this->once())->method('getType')->willReturn('second_rule');
        $this->secondPromotionRule->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->serviceRegistry->expects($this->exactly(2))->method('get')->willReturnMap([
            ['first_rule', $this->firstRuleChecker],
            ['second_rule', $this->secondRuleChecker],
        ]);
        $this->firstRuleChecker->expects($this->once())->method('isEligible')->willReturn(true);
        $this->secondRuleChecker->expects($this->once())->method('isEligible')->willReturn(false);

        $this->assertFalse($this->checker->isEligible($this->subject, $this->promotion));
    }

    public function testShouldNotCheckMoreRulesIfOneReturnFalse(): void
    {
        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->firstPromotionRule, $this->secondPromotionRule]),
        );
        $this->firstPromotionRule->expects($this->once())->method('getType')->willReturn('first_rule');
        $this->firstPromotionRule->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->secondPromotionRule->expects($this->never())->method('getType');
        $this->secondPromotionRule->expects($this->never())->method('getConfiguration');
        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->firstRuleChecker);
        $this->firstRuleChecker->expects($this->once())->method('isEligible')->willReturn(false);
        $this->secondRuleChecker->expects($this->never())->method('isEligible');

        $this->assertFalse($this->checker->isEligible($this->subject, $this->promotion));
    }
}
