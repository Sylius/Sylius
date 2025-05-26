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

namespace Tests\Sylius\Component\Core\Shipping\Checker\Rule;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\ShipmentInterface;
use Sylius\Component\Core\Shipping\Checker\Rule\OrderTotalGreaterThanOrEqualRuleChecker;
use Sylius\Component\Shipping\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Shipping\Model\ShipmentInterface as BaseShipmentInterface;

final class OrderTotalGreaterThanOrEqualRuleCheckerTest extends TestCase
{
    private MockObject&ShipmentInterface $subject;

    private MockObject&OrderInterface $order;

    private ChannelInterface&MockObject $channel;

    private OrderTotalGreaterThanOrEqualRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->subject = $this->createMock(ShipmentInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->ruleChecker = new OrderTotalGreaterThanOrEqualRuleChecker();
    }

    public function testShouldImplementRuleCheckerInterface(): void
    {
        $this->assertInstanceOf(RuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldBeInitializable(): void
    {
        $this->assertInstanceOf(OrderTotalGreaterThanOrEqualRuleChecker::class, $this->ruleChecker);
    }

    public function testShouldDenySubjectIfSubjectIsNotCoreShipment(): void
    {
        $this->assertFalse($this->ruleChecker->isEligible($this->createMock(BaseShipmentInterface::class), []));
    }

    public function testShouldRecognizeSubjectIfOrderTotalIsGreaterThanConfiguredAmount(): void
    {
        $this->subject->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->once())->method('getItemsTotal')->willReturn(101);
        $this->channel->expects($this->once())->method('getCode')->willReturn('CHANNEL');

        $this->assertTrue($this->ruleChecker->isEligible($this->subject, ['CHANNEL' => ['amount' => 100]]));
    }

    public function testShouldRecognizeSubjectIfOrderTotalIsEqualToConfiguredAmount(): void
    {
        $this->subject->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->once())->method('getItemsTotal')->willReturn(100);
        $this->channel->expects($this->once())->method('getCode')->willReturn('CHANNEL');

        $this->assertTrue($this->ruleChecker->isEligible($this->subject, ['CHANNEL' => ['amount' => 100]]));
    }

    public function testShouldDenySubjectIfOrderTotalIsLessThanConfiguredAmount(): void
    {
        $this->subject->expects($this->once())->method('getOrder')->willReturn($this->order);
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->order->expects($this->once())->method('getItemsTotal')->willReturn(99);
        $this->channel->expects($this->once())->method('getCode')->willReturn('CHANNEL');

        $this->assertFalse($this->ruleChecker->isEligible($this->subject, ['CHANNEL' => ['amount' => 100]]));
    }
}
