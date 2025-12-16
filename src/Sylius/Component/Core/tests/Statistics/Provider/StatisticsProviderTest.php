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

namespace Statistics\Provider;

namespace Tests\Sylius\Component\Core\Statistics\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProviderInterface;
use Sylius\Component\Core\Statistics\Provider\SalesStatisticsProviderInterface;
use Sylius\Component\Core\Statistics\Provider\StatisticsProvider;
use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface;
use Sylius\Component\Core\Statistics\ValueObject\BusinessActivitySummary;
use Sylius\Component\Core\Statistics\ValueObject\Statistics;

final class StatisticsProviderTest extends TestCase
{
    private MockObject&SalesStatisticsProviderInterface $salesProvider;

    private BusinessActivitySummaryProviderInterface&MockObject $businessActivitySummaryProvider;

    private StatisticsProvider $statisticsProvider;

    protected function setUp(): void
    {
        $this->salesProvider = $this->createMock(SalesStatisticsProviderInterface::class);
        $this->businessActivitySummaryProvider = $this->createMock(BusinessActivitySummaryProviderInterface::class);
        $this->statisticsProvider = new StatisticsProvider(
            $this->salesProvider,
            $this->businessActivitySummaryProvider,
        );
    }

    public function testShouldImplementStatisticsProviderInterface(): void
    {
        $this->assertInstanceOf(StatisticsProviderInterface::class, $this->statisticsProvider);
    }

    public function testShouldProvideStatistics(): void
    {
        $channel = $this->createMock(ChannelInterface::class);
        $businessActivitySummary = $this->createMock(BusinessActivitySummary::class);
        $datePeriod = $this->createMock(\DatePeriod::class);
        $this->salesProvider
            ->expects($this->once())
            ->method('provide')
            ->with('day', $datePeriod, $channel)
            ->willReturn([]);
        $this->businessActivitySummaryProvider
            ->expects($this->once())
            ->method('provide')
            ->with($datePeriod, $channel)
            ->willReturn($businessActivitySummary);

        $this->assertInstanceOf(
            Statistics::class,
            $this->statisticsProvider->provide('day', $datePeriod, $channel),
        );
    }
}
