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

namespace Tests\Sylius\Component\Shipping\Checker\Eligibility;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Registry\ServiceRegistryInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodEligibilityCheckerInterface;
use Sylius\Component\Shipping\Checker\Eligibility\ShippingMethodRulesEligibilityChecker;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShippingMethodInterface;
use Sylius\Component\Shipping\Model\ShippingMethodRuleInterface;
use Sylius\Component\Shipping\Model\ShippingSubjectInterface;

final class ShippingMethodRulesEligibilityCheckerTest extends TestCase
{
    private MockObject&ServiceRegistryInterface $rulesRegistry;

    private MockObject&ShippingSubjectInterface $shippingSubject;

    private MockObject&ShippingMethodInterface $shippingMethod;

    private MockObject&ShippingMethodRuleInterface $firstShippingMethodRule;

    private MockObject&ShippingMethodRuleInterface $secondShippingMethodRule;

    private MockObject&RuleCheckerInterface $firstRuleChecker;

    private MockObject&RuleCheckerInterface $secondRuleChecker;

    private ShippingMethodRulesEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->rulesRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->shippingSubject = $this->createMock(ShippingSubjectInterface::class);
        $this->shippingMethod = $this->createMock(ShippingMethodInterface::class);
        $this->firstShippingMethodRule = $this->createMock(ShippingMethodRuleInterface::class);
        $this->secondShippingMethodRule = $this->createMock(ShippingMethodRuleInterface::class);
        $this->firstRuleChecker = $this->createMock(RuleCheckerInterface::class);
        $this->secondRuleChecker = $this->createMock(RuleCheckerInterface::class);
        $this->checker = new ShippingMethodRulesEligibilityChecker($this->rulesRegistry);
    }

    public function testShouldImplementShippingMethodEligibilityCheckerInterface(): void
    {
        $this->assertInstanceOf(ShippingMethodEligibilityCheckerInterface::class, $this->checker);
    }

    public function testShouldRecognizeSubjectAsEligibleIfShippingMethodHasNoRules(): void
    {
        $this->shippingMethod->expects($this->once())->method('hasRules')->willReturn(false);

        $this->assertTrue($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
    }

    public function testShouldRecognizeSubjectAsEligibleIfAllOfShippingMethodRulesAreFullFilled(): void
    {
        $this->shippingMethod->expects($this->once())->method('hasRules')->willReturn(true);
        $this->shippingMethod->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->firstShippingMethodRule, $this->secondShippingMethodRule]),
        );
        $this->firstShippingMethodRule->expects($this->once())->method('getType')->willReturn('first_rule');
        $this->firstShippingMethodRule->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->secondShippingMethodRule->expects($this->once())->method('getType')->willReturn('second_rule');
        $this->secondShippingMethodRule->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->rulesRegistry->expects($this->exactly(2))->method('get')->willReturnMap([
                ['first_rule', $this->firstRuleChecker],
                ['second_rule', $this->secondRuleChecker],
            ]);
        $this->firstRuleChecker->expects($this->once())->method('isEligible')->with($this->shippingSubject, [])->willReturn(true);
        $this->secondRuleChecker->expects($this->once())->method('isEligible')->with($this->shippingSubject, [])->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
    }

    public function testShouldRecognizeSubjectAsNotEligibleIfAnyOfShippingMethodRuleIsNotFullFilled(): void
    {
        $this->shippingMethod->expects($this->once())->method('hasRules')->willReturn(true);
        $this->shippingMethod->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->firstShippingMethodRule, $this->secondShippingMethodRule]),
        );
        $this->firstShippingMethodRule->expects($this->once())->method('getType')->willReturn('first_rule');
        $this->firstShippingMethodRule->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->secondShippingMethodRule->expects($this->once())->method('getType')->willReturn('second_rule');
        $this->secondShippingMethodRule->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->rulesRegistry->expects($this->exactly(2))->method('get')->willReturnMap([
            ['first_rule', $this->firstRuleChecker],
            ['second_rule', $this->secondRuleChecker],
        ]);
        $this->firstRuleChecker->expects($this->once())->method('isEligible')->with($this->shippingSubject, [])->willReturn(true);
        $this->secondRuleChecker->expects($this->once())->method('isEligible')->with($this->shippingSubject, [])->willReturn(false);

        $this->assertFalse($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
    }

    public function testShouldNotCheckMoreRulesIfOneHasReturnedFalse(): void
    {
        $this->shippingMethod->expects($this->once())->method('hasRules')->willReturn(true);
        $this->shippingMethod->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->firstShippingMethodRule, $this->secondShippingMethodRule]),
        );
        $this->firstShippingMethodRule->expects($this->once())->method('getType')->willReturn('first_rule');
        $this->firstShippingMethodRule->expects($this->once())->method('getConfiguration')->willReturn([]);
        $this->secondShippingMethodRule->expects($this->never())->method('getType');
        $this->secondShippingMethodRule->expects($this->never())->method('getConfiguration');
        $this->rulesRegistry->expects($this->once())->method('get')->with('first_rule')->willReturn($this->firstRuleChecker);
        $this->firstRuleChecker->expects($this->once())->method('isEligible')->with($this->shippingSubject, [])->willReturn(false);
        $this->secondRuleChecker->expects($this->never())->method('isEligible')->with($this->shippingSubject, []);

        $this->assertFalse($this->checker->isEligible($this->shippingSubject, $this->shippingMethod));
    }
}
