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

namespace Tests\Sylius\Component\Core\Promotion\Checker\Eligibility;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\ChannelAwareConfigurationInterface;
use Sylius\Component\Core\Promotion\Checker\Eligibility\ChannelAwarePromotionRulesEligibilityChecker;
use Sylius\Component\Promotion\Checker\Eligibility\PromotionEligibilityCheckerInterface;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionRuleInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

#[CoversClass(ChannelAwarePromotionRulesEligibilityChecker::class)]
final class ChannelAwarePromotionRulesEligibilityCheckerTest extends TestCase
{
    private MockObject&RuleCheckerInterface $ruleChecker;

    private MockObject&PromotionRuleInterface $rule;

    private MockObject&PromotionInterface $promotion;

    private MockObject&OrderInterface $order;

    private MockObject&PromotionSubjectInterface $subject;

    private ChannelInterface&MockObject $channel;

    private MockObject&ServiceRegistryInterface $serviceRegistry;

    private ChannelAwarePromotionRulesEligibilityChecker $checker;

    protected function setUp(): void
    {
        $this->ruleChecker = $this->createMock(RuleCheckerInterface::class);
        $this->rule = $this->createMock(PromotionRuleInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->subject = $this->createMock(PromotionSubjectInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->serviceRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->checker = new ChannelAwarePromotionRulesEligibilityChecker($this->serviceRegistry);
    }

    public function testShouldImplementPromotionEligibilityCheckerInterfaceAndChannelAwareConfigurationInterface(): void
    {
        $this->assertInstanceOf(PromotionEligibilityCheckerInterface::class, $this->checker);
        $this->assertInstanceOf(ChannelAwareConfigurationInterface::class, $this->checker);
    }

    public function testShouldReturnEligibleIfPromotionHasNoRules(): void
    {
        $this->promotion->expects($this->once())->method('hasRules')->willReturn(false);
        $this->promotion->expects($this->never())->method('getRules');

        $this->assertTrue($this->checker->isEligible($this->order, $this->promotion));
    }

    public function testShouldReturnEligibleIfAllRulesPassWithNoChannelRestriction(): void
    {
        $secondRule = $this->createMock(PromotionRuleInterface::class);

        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->rule, $secondRule]),
        );

        $this->rule->expects($this->exactly(2))->method('getConfiguration')->willReturn([]);
        $this->rule->expects($this->once())->method('getType')->willReturn('item_total');
        $secondRule->expects($this->exactly(2))->method('getConfiguration')->willReturn([]);
        $secondRule->expects($this->once())->method('getType')->willReturn('cart_quantity');

        $this->serviceRegistry->expects($this->exactly(2))->method('get')->willReturn($this->ruleChecker);
        $this->ruleChecker->expects($this->exactly(2))->method('isEligible')->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->order, $this->promotion));
    }

    public function testShouldReturnNotEligibleIfAnyRuleFails(): void
    {
        $secondRule = $this->createMock(PromotionRuleInterface::class);

        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->rule, $secondRule]),
        );

        $this->rule->expects($this->exactly(2))->method('getConfiguration')->willReturn([]);
        $this->rule->expects($this->once())->method('getType')->willReturn('item_total');
        $secondRule->expects($this->exactly(2))->method('getConfiguration')->willReturn([]);
        $secondRule->expects($this->once())->method('getType')->willReturn('cart_quantity');

        $firstRuleChecker = $this->createMock(RuleCheckerInterface::class);
        $secondRuleChecker = $this->createMock(RuleCheckerInterface::class);

        $this->serviceRegistry
            ->expects($this->exactly(2))
            ->method('get')
            ->willReturnOnConsecutiveCalls($firstRuleChecker, $secondRuleChecker);

        $firstRuleChecker->expects($this->once())->method('isEligible')->willReturn(true);
        $secondRuleChecker->expects($this->once())->method('isEligible')->willReturn(false);

        $this->assertFalse($this->checker->isEligible($this->order, $this->promotion));
    }

    public function testShouldStopCheckingRulesAfterFirstFailure(): void
    {
        $secondRule = $this->createMock(PromotionRuleInterface::class);

        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->rule, $secondRule]),
        );

        $this->rule->expects($this->exactly(2))->method('getConfiguration')->willReturn([]);
        $this->rule->expects($this->once())->method('getType')->willReturn('item_total');
        $secondRule->expects($this->never())->method('getType');
        $secondRule->expects($this->never())->method('getConfiguration');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->ruleChecker);
        $this->ruleChecker->expects($this->once())->method('isEligible')->willReturn(false);

        $this->assertFalse($this->checker->isEligible($this->order, $this->promotion));
    }

    public function testShouldSkipRuleIfCurrentChannelNotInChannelsList(): void
    {
        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->rule]),
        );

        $this->rule
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('MOBILE');

        $this->serviceRegistry->expects($this->never())->method('get');

        $this->assertTrue($this->checker->isEligible($this->order, $this->promotion));
    }

    public function testShouldNotSkipRuleIfCurrentChannelIsInChannelsList(): void
    {
        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->rule]),
        );

        $this->rule
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);
        $this->rule->expects($this->once())->method('getType')->willReturn('item_total');

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('WEB');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->ruleChecker);
        $this->ruleChecker->expects($this->once())->method('isEligible')->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->order, $this->promotion));
    }

    public function testShouldNotSkipRuleIfChannelsListIsEmpty(): void
    {
        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->rule]),
        );

        $this->rule
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => []]);
        $this->rule->expects($this->once())->method('getType')->willReturn('item_total');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->ruleChecker);
        $this->ruleChecker->expects($this->once())->method('isEligible')->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->order, $this->promotion));
    }

    public function testShouldNotSkipRuleIfSubjectIsNotAnOrderInterface(): void
    {
        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->rule]),
        );

        $this->rule
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);
        $this->rule->expects($this->once())->method('getType')->willReturn('item_total');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->ruleChecker);
        $this->ruleChecker->expects($this->once())->method('isEligible')->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->subject, $this->promotion));
    }

    public function testShouldStripChannelsKeyFromConfigurationBeforePassingToRuleChecker(): void
    {
        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->rule]),
        );

        $this->rule
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn([
                'count' => 3,
                ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB'],
            ]);
        $this->rule->expects($this->once())->method('getType')->willReturn('cart_quantity');

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('WEB');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->ruleChecker);
        $this->ruleChecker
            ->expects($this->once())
            ->method('isEligible')
            ->with($this->order, ['count' => 3])
            ->willReturn(true);

        $this->assertTrue($this->checker->isEligible($this->order, $this->promotion));
    }

    public function testShouldReturnEligibleIfAllRulesForCurrentChannelAreSkipped(): void
    {
        $secondRule = $this->createMock(PromotionRuleInterface::class);

        $this->promotion->expects($this->once())->method('hasRules')->willReturn(true);
        $this->promotion->expects($this->once())->method('getRules')->willReturn(
            new ArrayCollection([$this->rule, $secondRule]),
        );

        $this->rule
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);
        $secondRule
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('MOBILE');

        $this->serviceRegistry->expects($this->never())->method('get');

        $this->assertTrue($this->checker->isEligible($this->order, $this->promotion));
    }
}
