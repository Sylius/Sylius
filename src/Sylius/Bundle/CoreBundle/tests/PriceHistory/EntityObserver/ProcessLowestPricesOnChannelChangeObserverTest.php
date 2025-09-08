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

namespace Tests\Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface;
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\EntityObserverInterface;
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\ProcessLowestPricesOnChannelChangeObserver;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ProcessLowestPricesOnChannelChangeObserverTest extends TestCase
{
    private ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface&MockObject $commandDispatcher;

    private ProcessLowestPricesOnChannelChangeObserver $observer;

    protected function setUp(): void
    {
        $this->commandDispatcher = $this->createMock(ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface::class);
        $this->observer = new ProcessLowestPricesOnChannelChangeObserver($this->commandDispatcher);
    }

    public function testIsAnEntityObserver(): void
    {
        $this->assertInstanceOf(EntityObserverInterface::class, $this->observer);
    }

    public function testDoesNotSupportNonChannelEntities(): void
    {
        $nonChannelEntity = $this->createMock(OrderInterface::class);
        $this->assertFalse($this->observer->supports($nonChannelEntity));
    }

    public function testDoesNotSupportChannelThatIsCurrentlyBeingProcessed(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('test');

        $ref = new \ReflectionObject($this->observer);
        $prop = $ref->getProperty('channelsCurrentlyProcessed');
        $prop->setAccessible(true);
        $prop->setValue($this->observer, ['test' => true]);

        $this->assertFalse($this->observer->supports($channel));
    }

    public function testDoesNotSupportChannelWithoutPriceHistoryConfig(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('test');
        $channel->method('getChannelPriceHistoryConfig')->willReturn(null);

        $this->assertFalse($this->observer->supports($channel));
    }

    public function testDoesNotSupportChannelWithExistingPriceHistoryConfig(): void
    {
        $config = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        $config->method('getId')->willReturn(42);

        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('test');
        $channel->method('getChannelPriceHistoryConfig')->willReturn($config);

        $this->assertFalse($this->observer->supports($channel));
    }

    public function testSupportsOnlyChannelsWithNewPriceHistoryConfig(): void
    {
        $config = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        $config->method('getId')->willReturn(null);

        $channel = $this->createMock(ChannelInterface::class);
        $channel->method('getCode')->willReturn('test');
        $channel->method('getChannelPriceHistoryConfig')->willReturn($config);

        $this->assertTrue($this->observer->supports($channel));
    }

    public function testObservedFields(): void
    {
        $this->assertSame(['channelPriceHistoryConfig'], $this->observer->observedFields());
    }

    public function testDelegatesProcessingToCommandDispatcher(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $this->commandDispatcher
            ->expects($this->once())
            ->method('applyWithinChannel')
            ->with($channel)
        ;

        $this->observer->onChange($channel);
    }

    public function testThrowsExceptionIfEntityIsNotAChannel(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $nonChannelEntity = $this->createMock(OrderInterface::class);
        $this->observer->onChange($nonChannelEntity);
    }
}
