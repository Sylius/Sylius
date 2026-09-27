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

use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Promotion\Action\PerChannelPromotionActionCommand;
use Sylius\Component\Promotion\Action\PromotionActionCommandInterface;
use Sylius\Component\Promotion\Model\PromotionInterface;
use Sylius\Component\Promotion\Model\PromotionSubjectInterface;
use Sylius\Resource\Exception\UnexpectedTypeException;

#[CoversClass(PerChannelPromotionActionCommand::class)]
final class PerChannelPromotionActionCommandTest extends TestCase
{
    private MockObject&PromotionActionCommandInterface $decorated;

    private ChannelInterface&MockObject $channel;

    private MockObject&OrderInterface $order;

    private MockObject&PromotionInterface $promotion;

    private PerChannelPromotionActionCommand $command;

    protected function setUp(): void
    {
        $this->decorated = $this->createMock(PromotionActionCommandInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->order = $this->createMock(OrderInterface::class);
        $this->promotion = $this->createMock(PromotionInterface::class);
        $this->command = new PerChannelPromotionActionCommand($this->decorated);
    }

    public function testShouldImplementPromotionActionCommandInterface(): void
    {
        $this->assertInstanceOf(PromotionActionCommandInterface::class, $this->command);
    }

    public function testShouldExecuteDecoratedCommandWithTheChannelConfiguration(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->decorated->expects($this->once())
            ->method('execute')
            ->with($this->order, ['percentage' => 0.2], $this->promotion)
            ->willReturn(true)
        ;

        $this->assertTrue($this->command->execute($this->order, ['WEB_US' => ['percentage' => 0.2]], $this->promotion));
    }

    public function testShouldNotExecuteDecoratedCommandIfThereIsNoConfigurationForTheOrderChannel(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->decorated->expects($this->never())->method('execute');

        $this->assertFalse($this->command->execute($this->order, ['WEB_EU' => ['percentage' => 0.2]], $this->promotion));
    }

    public function testShouldNotExecuteDecoratedCommandWhenOrderHasNoChannel(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn(null);
        $this->decorated->expects($this->never())->method('execute');

        $this->assertFalse($this->command->execute($this->order, ['WEB_US' => ['percentage' => 0.2]], $this->promotion));
    }

    public function testShouldNotExecuteDecoratedCommandWhenChannelConfigurationIsNotAnArray(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->decorated->expects($this->never())->method('execute');

        $this->assertFalse($this->command->execute($this->order, ['WEB_US' => 'not-an-array'], $this->promotion));
    }

    public function testShouldThrowExceptionWhenExecutingIfPromotionSubjectIsNotOrder(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->command->execute($this->createMock(PromotionSubjectInterface::class), [], $this->promotion);
    }

    public function testShouldRevertDecoratedCommandWithTheChannelConfiguration(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->decorated->expects($this->once())
            ->method('revert')
            ->with($this->order, ['percentage' => 0.2], $this->promotion)
        ;

        $this->command->revert($this->order, ['WEB_US' => ['percentage' => 0.2]], $this->promotion);
    }

    public function testShouldNotRevertDecoratedCommandIfThereIsNoConfigurationForTheOrderChannel(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->decorated->expects($this->never())->method('revert');

        $this->command->revert($this->order, ['WEB_EU' => ['percentage' => 0.2]], $this->promotion);
    }

    public function testShouldNotRevertDecoratedCommandWhenOrderHasNoChannel(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn(null);
        $this->decorated->expects($this->never())->method('revert');

        $this->command->revert($this->order, ['WEB_US' => ['percentage' => 0.2]], $this->promotion);
    }

    public function testShouldNotRevertDecoratedCommandWhenChannelConfigurationIsNotAnArray(): void
    {
        $this->order->expects($this->once())->method('getChannel')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('getCode')->willReturn('WEB_US');
        $this->decorated->expects($this->never())->method('revert');

        $this->command->revert($this->order, ['WEB_US' => 'not-an-array'], $this->promotion);
    }

    public function testShouldThrowExceptionWhenRevertingIfPromotionSubjectIsNotOrder(): void
    {
        $this->expectException(UnexpectedTypeException::class);

        $this->command->revert($this->createMock(PromotionSubjectInterface::class), [], $this->promotion);
    }
}
