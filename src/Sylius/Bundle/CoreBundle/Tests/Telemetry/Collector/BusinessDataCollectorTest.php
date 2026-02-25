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

namespace Sylius\Bundle\CoreBundle\Tests\Telemetry\Collector;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\Collector\BusinessDataCollector;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\CurrenciesData;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\LocalesData;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\MetricsCountsData;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\OrderMetricsData;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\OrdersBusinessData;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;

final class BusinessDataCollectorTest extends TestCase
{
    public function test_it_returns_correct_name(): void
    {
        $collector = new BusinessDataCollector([]);

        self::assertSame('business', $collector->getName());
    }

    public function test_it_is_enabled_by_default(): void
    {
        $collector = new BusinessDataCollector([]);

        self::assertTrue($collector->isEnabled());
    }

    public function test_it_can_be_disabled(): void
    {
        $collector = new BusinessDataCollector([], enabled: false);

        self::assertFalse($collector->isEnabled());
    }

    public function test_it_collects_data_from_providers(): void
    {
        $localesData = new LocalesData(['en_US', 'pl_PL'], [], 'en_US');

        $localesProvider = $this->createMock(DataProviderInterface::class);
        $localesProvider->method('provide')->willReturn($localesData);

        $collector = new BusinessDataCollector([$localesProvider]);

        $data = $collector->collect();

        self::assertArrayHasKey('locales', $data);
        self::assertSame(['en_US', 'pl_PL'], $data['locales']);
    }

    public function test_it_merges_data_from_multiple_providers(): void
    {
        $localesData = new LocalesData(['en_US', 'pl_PL'], [], 'en_US');
        $currenciesData = new CurrenciesData(['EUR', 'USD']);

        $localesProvider = $this->createMock(DataProviderInterface::class);
        $localesProvider->method('provide')->willReturn($localesData);

        $currenciesProvider = $this->createMock(DataProviderInterface::class);
        $currenciesProvider->method('provide')->willReturn($currenciesData);

        $collector = new BusinessDataCollector([$localesProvider, $currenciesProvider]);

        $data = $collector->collect();

        self::assertArrayHasKey('locales', $data);
        self::assertArrayHasKey('currencies', $data);
        self::assertSame(['en_US', 'pl_PL'], $data['locales']);
        self::assertSame(['EUR', 'USD'], $data['currencies']);
    }

    public function test_it_continues_on_provider_error(): void
    {
        $localesData = new LocalesData(['en_US'], [], 'en_US');

        $failingProvider = $this->createMock(DataProviderInterface::class);
        $failingProvider->method('provide')->willThrowException(new \RuntimeException('Provider error'));

        $workingProvider = $this->createMock(DataProviderInterface::class);
        $workingProvider->method('provide')->willReturn($localesData);

        $collector = new BusinessDataCollector([$failingProvider, $workingProvider]);

        $data = $collector->collect();

        self::assertArrayHasKey('locales', $data);
        self::assertSame(['en_US'], $data['locales']);
    }

    public function test_it_returns_empty_array_when_no_providers(): void
    {
        $collector = new BusinessDataCollector([]);

        $data = $collector->collect();

        self::assertSame([], $data);
    }

    public function test_it_merges_metrics_from_multiple_providers(): void
    {
        $metricsCountsData = new MetricsCountsData(
            customersCount: '100-1K',
            productsCount: '0-100',
            productVariantsCount: '1K-10K',
            ordersCount: 50000,
        );
        $ordersBusinessData = new OrdersBusinessData(
            gmvMonthly: ['USD' => '10K-50K'],
            aovMonthly: ['USD' => '250-500'],
            metrics: new OrderMetricsData('100-1K', '0-5', '5-10'),
        );

        $provider1 = $this->createMock(DataProviderInterface::class);
        $provider1->method('provide')->willReturn($metricsCountsData);

        $provider2 = $this->createMock(DataProviderInterface::class);
        $provider2->method('provide')->willReturn($ordersBusinessData);

        $collector = new BusinessDataCollector([$provider1, $provider2]);

        $data = $collector->collect();

        self::assertArrayHasKey('metrics', $data);
        self::assertArrayHasKey('gmv_monthly', $data);
        self::assertArrayHasKey('aov_monthly', $data);

        // Metrics from MetricsCountsData
        self::assertSame('100-1K', $data['metrics']['customers_count']);
        self::assertSame('0-100', $data['metrics']['products_count']);
        self::assertSame('1K-10K', $data['metrics']['product_variants_count']);
        self::assertSame(50000, $data['metrics']['orders_count']);

        // Metrics from OrdersBusinessData (order_metrics merged)
        self::assertSame('100-1K', $data['metrics']['orders_monthly_count']);
        self::assertSame('0-5', $data['metrics']['orders_monthly_avg_items']);
        self::assertSame('5-10', $data['metrics']['orders_monthly_avg_item_units']);
    }
}
