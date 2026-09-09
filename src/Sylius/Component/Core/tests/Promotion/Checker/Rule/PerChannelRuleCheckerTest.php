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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Checker\Rule\PerChannelRuleChecker;
use Sylius\Component\Promotion\Checker\Rule\RuleCheckerInterface;
use Sylius\Component\Promotion\Exception\UnsupportedTypeException;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;

#[CoversClass(PerChannelRuleChecker::class)]
final class PerChannelRuleCheckerTest extends TestCase
{
    private MockObject&RuleCheckerInterface $decorated;

    private ChannelInterface&MockObject $channel;

    private MockObject&OrderInterface $order;

    private PerChannelRuleChecker $ruleChecker;

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(RuleCheckerInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->ruleChecker = new PerChannelRuleChecker($this->decorated);
    }

    public function testShouldImplementRuleCheckerInterface(): void
    {
        $this->assertInstanceOf(RuleCheckerInterface::class, $this->ruleChecker);
    }

    public function testShouldDelegateToDecoratedCheckerWithTheChannelConfiguration(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->decorated->expects($this->once())
            ->method('isEligible')
            ->with($this->order, ['nth' => 5])
            ->willReturn(true)
        ;

        $this->assertTrue($this->ruleChecker->isEligible($this->order, ['WEB_US' => ['nth' => 5]]));
    }

    public function testShouldReturnDecoratedCheckerResultForTheOrderChannel(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->decorated->expects($this->once())
            ->method('isEligible')
            ->with($this->order, ['nth' => 5])
            ->willReturn(false)
        ;

        $this->assertFalse($this->ruleChecker->isEligible($this->order, ['WEB_US' => ['nth' => 5]]));
    }

    public function testShouldReturnFalseAndNotDelegateIfThereIsNoConfigurationForTheOrderChannel(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->decorated->expects($this->never())->method('isEligible');

        $this->assertFalse($this->ruleChecker->isEligible($this->order, ['WEB_EU' => ['nth' => 5]]));
    }

    public function testShouldReturnFalseAndNotDelegateWhenOrderHasNoChannel(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn(null);
        $this->decorated->expects($this->never())->method('isEligible');

        $this->assertFalse($this->ruleChecker->isEligible($this->order, ['WEB_US' => ['nth' => 5]]));
    }

    public function testShouldReturnFalseAndNotDelegateWhenChannelConfigurationIsNotAnArray(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->decorated->expects($this->never())->method('isEligible');

        $this->assertFalse($this->ruleChecker->isEligible($this->order, ['WEB_US' => 'not-an-array']));
    }

    public function testShouldThrowExceptionIfPromotionSubjectIsNotOrder(): void
    {
        $this->expectException(UnsupportedTypeException::class);

        $this->ruleChecker->isEligible($this->createMock(PromotionSubjectInterface::class), []);
    }
}
