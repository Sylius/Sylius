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

namespace Tests\Sylius\Component\Core\Factory;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Channel\Factory\ChannelFactoryInterface;
use Sylius\Component\Core\Factory\ChannelFactory;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Resource\Factory\FactoryInterface;

final class ChannelFactoryTest extends TestCase
{
    private FactoryInterface&MockObject $decoratedFactory;

    private FactoryInterface&MockObject $channelPriceHistoryConfigFactory;

    private ChannelInterface&MockObject $channel;

    private ChannelPriceHistoryConfigInterface&MockObject $channelPriceHistoryConfig;

    private ChannelFactory $factory;

    protected function setUp(): void
    {
        $this->decoratedFactory = $this->createMock(FactoryInterface::class);
        $this->channelPriceHistoryConfigFactory = $this->createMock(FactoryInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
        $this->channelPriceHistoryConfig = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        $this->factory = new ChannelFactory(
            $this->decoratedFactory,
            'order_items_based',
            $this->channelPriceHistoryConfigFactory,
        );
    }

    public function testShouldImplementChannelFactoryInterface(): void
    {
        $this->assertInstanceOf(ChannelFactoryInterface::class, $this->factory);
    }

    public function testShouldBeResourceFactory(): void
    {
        $this->assertInstanceOf(FactoryInterface::class, $this->factory);
    }

    public function testShouldCreateNewChannel(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('setTaxCalculationStrategy')->with('order_items_based');
        $this->channelPriceHistoryConfigFactory->expects($this->once())->method('createNew')->willReturn($this->channelPriceHistoryConfig);
        $this->channel->expects($this->once())->method('setChannelPriceHistoryConfig')->with($this->channelPriceHistoryConfig);

        $this->assertSame($this->channel, $this->factory->createNew());
    }

    public function testShouldCreateNewNamedChannel(): void
    {
        $this->decoratedFactory->expects($this->once())->method('createNew')->willReturn($this->channel);
        $this->channel->expects($this->once())->method('setTaxCalculationStrategy')->with('order_items_based');
        $this->channelPriceHistoryConfigFactory->expects($this->once())->method('createNew')->willReturn($this->channelPriceHistoryConfig);
        $this->channel->expects($this->once())->method('setChannelPriceHistoryConfig')->with($this->channelPriceHistoryConfig);
        $this->channel->expects($this->once())->method('setName')->with('Web');

        $this->assertSame($this->channel, $this->factory->createNamed('Web'));
    }
}
