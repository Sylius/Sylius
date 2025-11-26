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

namespace Tests\Sylius\Bundle\CoreBundle\PriceHistory\Logger;

use Doctrine\Persistence\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLogger;
use Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLoggerInterface;
use Sylius\Component\Core\Factory\ChannelPricingLogEntryFactoryInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntryInterface;
use Symfony\Component\Clock\ClockInterface;

final class PriceChangeLoggerTest extends TestCase
{
    private ChannelPricingLogEntryFactoryInterface&MockObject $logEntryFactory;

    private MockObject&ObjectManager $logEntryManager;

    private ClockInterface&MockObject $clock;

    private PriceChangeLogger $logger;

    protected function setUp(): void
    {
        $this->logEntryFactory = $this->createMock(ChannelPricingLogEntryFactoryInterface::class);
        $this->logEntryManager = $this->createMock(ObjectManager::class);
        $this->clock = $this->createMock(ClockInterface::class);

        $this->logger = new PriceChangeLogger($this->logEntryFactory, $this->logEntryManager, $this->clock);
    }

    public function testImplementsPriceChangeLoggerInterface(): void
    {
        $this->assertInstanceOf(PriceChangeLoggerInterface::class, $this->logger);
    }

    public function testThrowsExceptionWhenThereIsNoPrice(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $channelPricing->method('getPrice')->willReturn(null);

        $this->clock->expects($this->never())->method('now');
        $this->logEntryFactory->expects($this->never())->method('create');
        $this->logEntryManager->expects($this->never())->method('persist');

        $this->expectException(\InvalidArgumentException::class);
        $this->logger->log($channelPricing);
    }

    public function testLogsPriceChange(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $logEntry = $this->createMock(ChannelPricingLogEntryInterface::class);
        $now = new \DateTimeImmutable();
        $price = 1000;
        $originalPrice = 1200;

        $channelPricing->method('getPrice')->willReturn($price);
        $channelPricing->method('getOriginalPrice')->willReturn($originalPrice);

        $this->clock->expects($this->once())->method('now')->willReturn($now);

        $this->logEntryFactory
            ->expects($this->once())
            ->method('create')
            ->with($channelPricing, $now, $price, $originalPrice)
            ->willReturn($logEntry)
        ;

        $this->logEntryManager->expects($this->once())->method('persist')->with($logEntry);

        $this->logger->log($channelPricing);
    }
}
