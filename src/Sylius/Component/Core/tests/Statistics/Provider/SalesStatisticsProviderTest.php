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

namespace Tests\Sylius\Component\Core\Statistics\Provider;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\OrdersTotalsProviderInterface;
use Sylius\Component\Core\Statistics\Provider\SalesProviderInterface;
use Sylius\Component\Core\Statistics\Provider\SalesStatisticsProvider;
use Sylius\Component\Core\Statistics\Registry\OrdersTotalsProviderRegistryInterface;
use Sylius\Component\Core\Statistics\Registry\StatisticsProviderRegistryInterface;
use Symfony\Contracts\Cache\CacheInterface;

final class SalesStatisticsProviderTest extends TestCase
{
    private MockObject&OrdersTotalsProviderRegistryInterface $ordersTotalsProviderRegistry;

    private CacheInterface&MockObject $cache;

    private ChannelInterface&MockObject $channel;

    protected function setUp(): void
    {
        $this->ordersTotalsProviderRegistry = $this->createMock(OrdersTotalsProviderRegistryInterface::class);
        $this->cache = $this->createMock(CacheInterface::class);
        $this->channel = $this->createMock(ChannelInterface::class);
    }

    public function testThrowsExceptionWhenIntervalTypeIsUnknown(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $provider = new SalesStatisticsProvider($this->ordersTotalsProviderRegistry, [
            'day' => ['interval' => 'P1D', 'period_format' => 'Y-m-d'],
        ]);

        $provider->provide(
            'unknown',
            new \DatePeriod(new \DateTimeImmutable(), new \DateInterval('P1D'), 1),
            $this->channel,
        );
    }

    public function testThrowsExceptionWhenRegistryThrowsException(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->ordersTotalsProviderRegistry
            ->method('getByType')
            ->willThrowException(new \InvalidArgumentException());

        $provider = new SalesStatisticsProvider($this->ordersTotalsProviderRegistry, [
            'day' => ['interval' => 'P1D', 'period_format' => 'Y-m-d'],
        ]);

        $provider->provide(
            'day',
            new \DatePeriod(new \DateTimeImmutable(), new \DateInterval('P1D'), 1),
            $this->channel,
        );
    }

    public function testReturnsEmptyArrayWhenNoStatisticsAvailable(): void
    {
        $provider = $this->createMock(OrdersTotalsProviderInterface::class);
        $provider->method('provideForPeriodInChannel')->willReturn([]);

        $this->ordersTotalsProviderRegistry->method('getByType')->with('day')->willReturn($provider);

        $provider = new SalesStatisticsProvider($this->ordersTotalsProviderRegistry, [
            'day' => ['interval' => 'P1D', 'period_format' => 'Y-m-d'],
        ]);

        $result = $provider->provide(
            'day',
            new \DatePeriod(new \DateTimeImmutable(), new \DateInterval('P1D'), 1),
            $this->channel,
        );

        $this->assertSame([], $result);
    }

    public function testReturnsFormattedStatistics(): void
    {
        $salesData = [
            ['period' => new \DateTimeImmutable('2020-01-01'), 'total' => 1000],
            ['period' => new \DateTimeImmutable('2020-01-02'), 'total' => 2000],
        ];

        $provider = $this->createMock(OrdersTotalsProviderInterface::class);
        $provider->method('provideForPeriodInChannel')->willReturn($salesData);

        $this->ordersTotalsProviderRegistry->method('getByType')->with('day')->willReturn($provider);

        $provider = new SalesStatisticsProvider($this->ordersTotalsProviderRegistry, [
            'day' => ['interval' => 'P1D', 'period_format' => 'Y-m-d'],
        ]);

        $result = $provider->provide(
            'day',
            new \DatePeriod(new \DateTimeImmutable(), new \DateInterval('P1D'), 2),
            $this->channel,
        );

        $this->assertSame([
            ['period' => '2020-01-01', 'total' => 1000],
            ['period' => '2020-01-02', 'total' => 2000],
        ], $result);
    }

    public function testMergesDataFromMultipleRegistries(): void
    {
        $firstRegistry = $this->createMock(StatisticsProviderRegistryInterface::class);
        $secondRegistry = $this->createMock(StatisticsProviderRegistryInterface::class);

        $firstPeriod = new \DateTimeImmutable('2020-01-01');
        $secondPeriod = new \DateTimeImmutable('2020-01-02');

        $firstProvider = [
            ['period' => $firstPeriod, 'total' => 1000],
            ['period' => $secondPeriod, 'total' => 2000],
        ];
        $secondProvider = [
            ['period' => $firstPeriod, 'orders_count' => 5],
            ['period' => $secondPeriod, 'orders_count' => 10],
        ];

        $firstRegistry->method('getByType')->willReturn(new class($firstProvider) implements SalesProviderInterface {
            /** @param array<array{period: \DateTimeImmutable, ...}> $data */
            public function __construct(private readonly array $data)
            {
            }

            public function provideForPeriodInChannel(\DatePeriod $period, ChannelInterface $channel): array
            {
                return $this->data;
            }
        });

        $secondRegistry->method('getByType')->willReturn(new class($secondProvider) implements SalesProviderInterface {
            /** @param array<array{period: \DateTimeImmutable, ...}> $data */
            public function __construct(private readonly array $data)
            {
            }

            public function provideForPeriodInChannel(\DatePeriod $period, ChannelInterface $channel): array
            {
                return $this->data;
            }
        });

        $provider = new SalesStatisticsProvider(
            $this->ordersTotalsProviderRegistry,
            ['day' => ['interval' => 'P1D', 'period_format' => 'Y-m-d']],
            [$firstRegistry, $secondRegistry],
        );

        $result = $provider->provide(
            'day',
            new \DatePeriod(new \DateTimeImmutable(), new \DateInterval('P1D'), 2),
            $this->channel,
        );

        $this->assertSame([
            ['period' => '2020-01-01', 'total' => 1000, 'orders_count' => 5],
            ['period' => '2020-01-02', 'total' => 2000, 'orders_count' => 10],
        ], $result);
    }

    public function testUsesCacheWhenAvailable(): void
    {
        $salesData = [
            ['period' => new \DateTimeImmutable('2024-01-01'), 'total' => 999],
        ];

        $provider = $this->createMock(OrdersTotalsProviderInterface::class);
        $provider->method('provideForPeriodInChannel')->willReturn($salesData);

        $this->ordersTotalsProviderRegistry->method('getByType')->willReturn($provider);

        $this->cache
            ->expects($this->once())
            ->method('get')
            ->with(
                $this->callback(fn (string $key) => str_starts_with($key, 'sylius_sales_statistics.day.')),
                $this->callback(fn (callable $callback) => true),
            )
            ->willReturn([
                ['period' => '2024-01-01', 'total' => 999],
            ]);

        $provider = new SalesStatisticsProvider(
            $this->ordersTotalsProviderRegistry,
            ['day' => ['interval' => 'P1D', 'period_format' => 'Y-m-d']],
            [],
            $this->cache,
        );

        $result = $provider->provide(
            'day',
            new \DatePeriod(new \DateTimeImmutable('2024-01-01'), new \DateInterval('P1D'), 1),
            $this->channel,
        );

        $this->assertSame([['period' => '2024-01-01', 'total' => 999]], $result);
    }
}
