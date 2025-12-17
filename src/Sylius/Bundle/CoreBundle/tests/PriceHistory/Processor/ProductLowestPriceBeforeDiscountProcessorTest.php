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

namespace Tests\Sylius\Bundle\CoreBundle\PriceHistory\Processor;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessor;
use Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface;
use Sylius\Component\Channel\Repository\ChannelRepositoryInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\ChannelPriceHistoryConfigInterface;
use Sylius\Component\Core\Model\ChannelPricingInterface;
use Sylius\Component\Core\Model\ChannelPricingLogEntry;
use Sylius\Component\Core\Repository\ChannelPricingLogEntryRepositoryInterface;

final class ProductLowestPriceBeforeDiscountProcessorTest extends TestCase
{
    private ChannelPricingLogEntryRepositoryInterface&MockObject $logEntryRepository;

    private ChannelRepositoryInterface&MockObject $channelRepository;

    private ProductLowestPriceBeforeDiscountProcessor $processor;

    protected function setUp(): void
    {
        $this->logEntryRepository = $this->createMock(ChannelPricingLogEntryRepositoryInterface::class);
        $this->channelRepository = $this->createMock(ChannelRepositoryInterface::class);
        $this->processor = new ProductLowestPriceBeforeDiscountProcessor(
            $this->logEntryRepository,
            $this->channelRepository,
        );
    }

    public function testImplementsProductLowestPriceBeforeDiscountProcessorInterface(): void
    {
        $this->assertInstanceOf(ProductLowestPriceBeforeDiscountProcessorInterface::class, $this->processor);
    }

    public function testSetsNullIfOriginalPriceIsNull(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $channelPricing->method('getOriginalPrice')->willReturn(null);
        $channelPricing->method('getPrice')->willReturn(2100);

        $channelPricing->expects($this->once())->method('setLowestPriceBeforeDiscount')->with(null);

        $this->processor->process($channelPricing);
    }

    public function testSetsNullIfPriceEqualsOriginal(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $channelPricing->method('getOriginalPrice')->willReturn(2100);
        $channelPricing->method('getPrice')->willReturn(2100);

        $channelPricing->expects($this->once())->method('setLowestPriceBeforeDiscount')->with(null);

        $this->processor->process($channelPricing);
    }

    public function testSetsNullIfPriceGreaterThanOriginal(): void
    {
        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $channelPricing->method('getOriginalPrice')->willReturn(2100);
        $channelPricing->method('getPrice')->willReturn(3700);

        $channelPricing->expects($this->once())->method('setLowestPriceBeforeDiscount')->with(null);

        $this->processor->process($channelPricing);
    }

    public function testSetsNullIfNoLogEntryFound(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $config = $this->createMock(ChannelPriceHistoryConfigInterface::class);

        $channel->method('getChannelPriceHistoryConfig')->willReturn($config);

        $channelPricing = $this->createMock(ChannelPricingInterface::class);

        $channelPricing->method('getOriginalPrice')->willReturn(3700);
        $channelPricing->method('getPrice')->willReturn(2100);
        $channelPricing->method('getChannelCode')->willReturn('WEB');
        $this->channelRepository->method('findOneByCode')->with('WEB')->willReturn($channel);

        $this->logEntryRepository
            ->method('findLatestOneByChannelPricing')
            ->with($channelPricing)
            ->willReturn(null)
        ;

        $channelPricing->expects($this->once())->method('setLowestPriceBeforeDiscount')->with(null);

        $this->processor->process($channelPricing);
    }

    public function testSetsLowestPriceBeforeDiscountIfPromotionApplied(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $config = $this->createMock(ChannelPriceHistoryConfigInterface::class);
        $channel->method('getChannelPriceHistoryConfig')->willReturn($config);
        $config->method('getLowestPriceForDiscountedProductsCheckingPeriod')->willReturn(30);

        $channelPricing = $this->createMock(ChannelPricingInterface::class);
        $channelPricing->method('getOriginalPrice')->willReturn(3700);
        $channelPricing->method('getPrice')->willReturn(2100);
        $channelPricing->method('getChannelCode')->willReturn('WEB');

        $logEntry = $this->createMock(ChannelPricingLogEntry::class);
        $logEntry->method('getId')->willReturn(1234);
        $logEntry->method('getChannelPricing')->willReturn($channelPricing);
        $now = new \DateTimeImmutable();
        $logEntry->method('getLoggedAt')->willReturn($now);

        $this->logEntryRepository->method('findLatestOneByChannelPricing')->with($channelPricing)->willReturn($logEntry);
        $this->channelRepository->method('findOneByCode')->with('WEB')->willReturn($channel);

        $expectedStartDate = $now->sub(new \DateInterval('P30D'));

        $this->logEntryRepository
            ->expects($this->once())
            ->method('findLowestPriceInPeriod')
            ->with(1234, $channelPricing, $this->callback(fn ($value) => $value instanceof \DateTimeInterface &&
                $value->format('Y-m-d') === $expectedStartDate->format('Y-m-d')))
            ->willReturn(6900)
        ;

        $channelPricing->expects($this->once())->method('setLowestPriceBeforeDiscount')->with(6900);

        $this->processor->process($channelPricing);
    }
}
