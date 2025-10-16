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
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserver;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserverTest extends TestCase
{
    private ChannelRepositoryInterface&MockObject $channelRepository;

    private ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface&MockObject $commandDispatcher;

    private ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserver $observer;

    protected function setUp(): void
    {
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->commandDispatcher = $this->createMock(ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface::class);
        $this->observer = new ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserver(
            $this->channelRepository,
            $this->commandDispatcher,
        );
    }

    public function testIsAnEntityObserver(): void
    {
        $this->assertInstanceOf(EntityObserverInterface::class, $this->observer);
    }

    public function testDoesNotSupportAnythingOtherThanChannelPriceHistoryConfig(): void
    {
        $order = $this->createMock(OrderInterface::class);
        $this->assertFalse($this->observer->supports($order));
    }

    public function testDoesNotSupportNewConfigs(): void
    {
        $config = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        $config->method('getId')->willReturn(null);

        $this->assertFalse($this->observer->supports($config));
    }

    public function testOnlySupportsExistingConfigs(): void
    {
        $config = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        $config->method('getId')->willReturn(1);

        $this->assertTrue($this->observer->supports($config));
    }

    public function testDoesNotSupportConfigThatIsCurrentlyBeingProcessed(): void
    {
        $config = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        $config->method('getId')->willReturn(1);

        $reflection = new \ReflectionObject($this->observer);
        $property = $reflection->getProperty('configsCurrentlyProcessed');
        $property->setAccessible(true);
        $property->setValue($this->observer, [1 => true]);

        $this->assertFalse($this->observer->supports($config));
    }

    public function testObservedFields(): void
    {
        $this->assertSame(
            ['lowestPriceForDiscountedProductsCheckingPeriod'],
            $this->observer->observedFields(),
        );
    }

    public function testThrowsExceptionWhenEntityIsNotAChannelPriceHistoryConfig(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $order = $this->createMock(OrderInterface::class);
        $this->observer->onChange($order);
    }

    public function testDoesNothingWhenNoChannelFoundForConfig(): void
    {
        $config = $this->createMock(ChannelPriceHistoryConfigInterface::class);

        $this->channelRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['channelPriceHistoryConfig' => $config])
            ->willReturn(null)
        ;

        $this->commandDispatcher
            ->expects($this->never())
            ->method('applyWithinChannel')
        ;

        $this->observer->onChange($config);
    }

    public function testDelegatesProcessingToCommandDispatcher(): void
    {
        $config = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        $channel = $this->createMock(ChannelInterface::class);

        $this->channelRepository
            ->expects($this->once())
            ->method('findOneBy')
            ->with(['channelPriceHistoryConfig' => $config])
            ->willReturn($channel)
        ;

        $this->commandDispatcher
            ->expects($this->once())
            ->method('applyWithinChannel')
            ->with($channel)
        ;

        $this->observer->onChange($config);
    }
}
