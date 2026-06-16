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

namespace Tests\Sylius\Component\Core\Promotion\Action;

use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Action\ChannelAwarePromotionApplicator;
use Sylius\Component\Core\Promotion\ChannelAwareConfigurationInterface;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Action\PromotionApplicatorInterface;
use Sylius\Component\Promotion\Model\PromotionActionInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Component\Registry\ServiceRegistryInterface;

#[CoversClass(ChannelAwarePromotionApplicator::class)]
final class ChannelAwarePromotionApplicatorTest extends TestCase
{
    private MockObject&PromotionActionCommandInterface $actionCommand;

    private MockObject&PromotionActionInterface $action;

    private MockObject&PromotionInterface $promotion;

    private MockObject&OrderInterface $order;

    private MockObject&PromotionSubjectInterface $subject;

    private ChannelInterface&MockObject $channel;

    private MockObject&ServiceRegistryInterface $serviceRegistry;

    private ChannelAwarePromotionApplicator $applicator;

    protected function setUp(): void
    {
        $this->actionCommand = $this->createMock(PromotionActionCommandInterface::class);
        $this->action = $this->createMock(PromotionActionInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->subject = $this->createMock(PromotionSubjectInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->serviceRegistry = $this->createMock(ServiceRegistryInterface::class);
        $this->applicator = new ChannelAwarePromotionApplicator($this->serviceRegistry);
    }

    public function testShouldImplementPromotionApplicatorInterfaceAndChannelAwareConfigurationInterface(): void
    {
        $this->assertInstanceOf(PromotionApplicatorInterface::class, $this->applicator);
        $this->assertInstanceOf(ChannelAwareConfigurationInterface::class, $this->applicator);
    }

    public function testShouldExecuteActionAndAddPromotionIfActionReturnsTrue(): void
    {
        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action]),
        );

        $this->action->expects($this->exactly(2))->method('getConfiguration')->willReturn([]);
        $this->action->expects($this->once())->method('getType')->willReturn('order_percentage_discount');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->actionCommand);
        $this->actionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->order, [], $this->promotion)
            ->willReturn(true);

        $this->order->expects($this->once())->method('addPromotion')->with($this->promotion);

        $this->applicator->apply($this->order, $this->promotion);
    }

    public function testShouldNotAddPromotionIfActionReturnsFalse(): void
    {
        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action]),
        );

        $this->action->expects($this->exactly(2))->method('getConfiguration')->willReturn([]);
        $this->action->expects($this->once())->method('getType')->willReturn('order_percentage_discount');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->actionCommand);
        $this->actionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->order, [], $this->promotion)
            ->willReturn(false);

        $this->order->expects($this->never())->method('addPromotion');

        $this->applicator->apply($this->order, $this->promotion);
    }

    public function testShouldSkipActionDuringApplyIfChannelNotMatching(): void
    {
        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action]),
        );

        $this->action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('MOBILE');

        $this->serviceRegistry->expects($this->never())->method('get');
        $this->actionCommand->expects($this->never())->method('execute');
        $this->order->expects($this->never())->method('addPromotion');

        $this->applicator->apply($this->order, $this->promotion);
    }

    public function testShouldExecuteActionDuringApplyIfChannelMatches(): void
    {
        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action]),
        );

        $this->action
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);
        $this->action->expects($this->once())->method('getType')->willReturn('order_percentage_discount');

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('WEB');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->actionCommand);
        $this->actionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->order, [], $this->promotion)
            ->willReturn(true);

        $this->order->expects($this->once())->method('addPromotion')->with($this->promotion);

        $this->applicator->apply($this->order, $this->promotion);
    }

    public function testShouldExecuteActionIfChannelsListIsEmpty(): void
    {
        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action]),
        );

        $this->action
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => []]);
        $this->action->expects($this->once())->method('getType')->willReturn('order_percentage_discount');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->actionCommand);
        $this->actionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->order, [], $this->promotion)
            ->willReturn(true);

        $this->order->expects($this->once())->method('addPromotion')->with($this->promotion);

        $this->applicator->apply($this->order, $this->promotion);
    }

    public function testShouldNotSkipActionIfSubjectIsNotOrderInterface(): void
    {
        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action]),
        );

        $this->action
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);
        $this->action->expects($this->once())->method('getType')->willReturn('order_percentage_discount');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->actionCommand);
        $this->actionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->subject, [], $this->promotion)
            ->willReturn(true);

        $this->subject->expects($this->once())->method('addPromotion')->with($this->promotion);

        $this->applicator->apply($this->subject, $this->promotion);
    }

    public function testShouldStripChannelsKeyFromConfigurationDuringApply(): void
    {
        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action]),
        );

        $this->action
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn([
                'amount' => 100,
                ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB'],
            ]);
        $this->action->expects($this->once())->method('getType')->willReturn('order_fixed_discount');

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('WEB');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->actionCommand);
        $this->actionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->order, ['amount' => 100], $this->promotion)
            ->willReturn(true);

        $this->order->expects($this->once())->method('addPromotion')->with($this->promotion);

        $this->applicator->apply($this->order, $this->promotion);
    }

    public function testShouldAddPromotionIfAtLeastOneActionAppliedDespiteOtherBeingSkipped(): void
    {
        $secondAction = $this->createMock(PromotionActionInterface::class);

        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action, $secondAction]),
        );

        $this->action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);

        $secondAction->expects($this->exactly(2))->method('getConfiguration')->willReturn([]);
        $secondAction->expects($this->once())->method('getType')->willReturn('order_percentage_discount');

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('MOBILE');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->actionCommand);
        $this->actionCommand
            ->expects($this->once())
            ->method('execute')
            ->with($this->order, [], $this->promotion)
            ->willReturn(true);

        $this->order->expects($this->once())->method('addPromotion')->with($this->promotion);

        $this->applicator->apply($this->order, $this->promotion);
    }

    public function testShouldNotAddPromotionIfAllActionsSkippedDueToChannel(): void
    {
        $secondAction = $this->createMock(PromotionActionInterface::class);

        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action, $secondAction]),
        );

        $this->action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);
        $secondAction
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('MOBILE');

        $this->serviceRegistry->expects($this->never())->method('get');
        $this->order->expects($this->never())->method('addPromotion');

        $this->applicator->apply($this->order, $this->promotion);
    }

    public function testShouldSkipActionDuringRevertIfChannelNotMatching(): void
    {
        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action]),
        );

        $this->action
            ->expects($this->once())
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('MOBILE');

        $this->serviceRegistry->expects($this->never())->method('get');
        $this->actionCommand->expects($this->never())->method('revert');
        $this->order->expects($this->once())->method('removePromotion')->with($this->promotion);

        $this->applicator->revert($this->order, $this->promotion);
    }

    public function testShouldRevertActionIfChannelMatches(): void
    {
        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action]),
        );

        $this->action
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn([ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB']]);
        $this->action->expects($this->once())->method('getType')->willReturn('order_percentage_discount');

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('WEB');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->actionCommand);
        $this->actionCommand
            ->expects($this->once())
            ->method('revert')
            ->with($this->order, [], $this->promotion);

        $this->order->expects($this->once())->method('removePromotion')->with($this->promotion);

        $this->applicator->revert($this->order, $this->promotion);
    }

    public function testShouldStripChannelsKeyFromConfigurationDuringRevert(): void
    {
        $this->promotion->expects($this->once())->method('getActions')->willReturn(
            new ArrayCollection([$this->action]),
        );

        $this->action
            ->expects($this->exactly(2))
            ->method('getConfiguration')
            ->willReturn([
                'amount' => 100,
                ChannelAwareConfigurationInterface::CHANNELS_CONFIGURATION_KEY => ['WEB'],
            ]);
        $this->action->expects($this->once())->method('getType')->willReturn('order_fixed_discount');

        $this->order->method('getChannel')->willReturn($this->channel);
        $this->channel->method('getCode')->willReturn('WEB');

        $this->serviceRegistry->expects($this->once())->method('get')->willReturn($this->actionCommand);
        $this->actionCommand
            ->expects($this->once())
            ->method('revert')
            ->with($this->order, ['amount' => 100], $this->promotion);

        $this->order->expects($this->once())->method('removePromotion')->with($this->promotion);

        $this->applicator->revert($this->order, $this->promotion);
    }
}
