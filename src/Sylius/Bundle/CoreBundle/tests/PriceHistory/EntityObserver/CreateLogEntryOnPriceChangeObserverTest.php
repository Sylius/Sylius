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

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\CreateLogEntryOnPriceChangeObserver;
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\EntityObserverInterface;
use Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLoggerInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\OrderInterface;

final class CreateLogEntryOnPriceChangeObserverTest extends TestCase
{
    private MockObject&PriceChangeLoggerInterface $priceChangeLogger;

    private CreateLogEntryOnPriceChangeObserver $createLogEntryOnPriceChangeObserver;

    protected function setUp(): void
    {
        $this->priceChangeLogger = $this->createMock(PriceChangeLoggerInterface::class);
        $this->createLogEntryOnPriceChangeObserver = new CreateLogEntryOnPriceChangeObserver($this->priceChangeLogger);
    }

    public function testImplementsOnEntityChangeInterface(): void
    {
        $this->assertInstanceOf(EntityObserverInterface::class, $this->createLogEntryOnPriceChangeObserver);
    }

    public function testSupportsChannelPricingWithPriceSpecifiedOnly(): void
    {
        $channelPricingWithPrice = $this->createMock(ChannelPricingInterface::class);
        $channelPricingWithoutPrice = $this->createMock(ChannelPricingInterface::class);
        $order = $this->createMock(OrderInterface::class);

        $channelPricingWithPrice->expects($this->once())->method('getPrice')->willReturn(1000);

        $this->assertTrue($this->createLogEntryOnPriceChangeObserver->supports($channelPricingWithPrice));
        $this->assertFalse($this->createLogEntryOnPriceChangeObserver->supports($channelPricingWithoutPrice));
        $this->assertFalse($this->createLogEntryOnPriceChangeObserver->supports($order));
    }

    public function testSupportsPriceAndOriginalPriceFields(): void
    {
        $this->assertSame(['price', 'originalPrice'], $this->createLogEntryOnPriceChangeObserver->observedFields());
    }

    public function testLogsPriceChange(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $this->priceChangeLogger->expects($this->once())->method('log')->with($channelPricing);

        $this->createLogEntryOnPriceChangeObserver->onChange($channelPricing);
    }

    public function testThrowsAnErrorIfEntityIsNotChannelPricing(): void
    {
        $channel = $this->createMock(ChannelInterface::class);

        $this->priceChangeLogger->expects($this->never())->method('log')->with($channel);

        $this->expectException(InvalidArgumentException::class);

        $this->createLogEntryOnPriceChangeObserver->onChange($channel);
    }
}
