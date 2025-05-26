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

namespace Tests\Sylius\Component\Core\Promotion\Checker\Rule;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\ItemTotalRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

final class ItemTotalRuleCheckerTest extends TestCase
{
    private ChannelInterface&MockObject $channel;

    private MockObject&OrderInterface $order;

    private ItemTotalRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->ruleChecker = new ItemTotalRuleChecker();
    }

    public function testShouldImplementRuleCheckerInterface(): void
    {
        $this->assertInstanceOf(RuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldRecognizeSubjectAsNotEligibleIfTheSubjectTotalIsLessThanConfigured(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->order->expects($this->once())->method('getPromotionSubjectTotal')->willReturn(400);

        $this->assertFalse($this->ruleChecker->isEligible($this->order, ['WEB_US' => ['amount' => 500]]));
    }

    public function testShouldRecognizeSubjectAsEligibleIfTheSubjectTotalIsGreaterThanConfigured(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->order->expects($this->once())->method('getPromotionSubjectTotal')->willReturn(600);

        $this->assertTrue($this->ruleChecker->isEligible($this->order, ['WEB_US' => ['amount' => 500]]));
    }

    public function testShouldRecognizeSubjectAsEligibleIfTheSubjectTotalIsConfigured(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->order->expects($this->once())->method('getPromotionSubjectTotal')->willReturn(500);

        $this->assertTrue($this->ruleChecker->isEligible($this->order, ['WEB_US' => ['amount' => 500]]));
    }

    public function testShouldReturnFalseIfThereIsNoConfgiurationForOrderChannel(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');

        $this->assertFalse($this->ruleChecker->isEligible($this->order, []));
    }

    public function testShouldThrowExceptionIfPromotionSubjectIsNotOrder(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->ruleChecker->isEligible($this->createMock(PromotionSubjectInterface::class), []);
    }
}
