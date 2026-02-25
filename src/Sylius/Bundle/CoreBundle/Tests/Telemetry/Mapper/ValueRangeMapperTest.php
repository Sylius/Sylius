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

namespace Sylius\Bundle\CoreBundle\Tests\Telemetry\Mapper;

use PHPUnit\Framework\TestCase;
use Sylius\Component\Core\Telemetry\Mapper\ValueRangeMapper;

final class ValueRangeMapperTest extends TestCase
{
    /**
     * @dataProvider gmvDataProvider
     */
    public function test_it_maps_gmv_to_correct_range(int|float $value, string $expectedRange): void
    {
        self::assertSame($expectedRange, ValueRangeMapper::mapGmv($value));
    }

    public static function gmvDataProvider(): array
    {
        return [
            [0, '0-10K'],
            [5000, '0-10K'],
            [9999, '0-10K'],
            [10000, '10K-50K'],
            [25000, '10K-50K'],
            [49999, '10K-50K'],
            [50000, '50K-100K'],
            [100000, '100K-500K'],
            [250000, '100K-500K'],
            [500000, '500K-1M'],
            [1000000, '1M-5M'],
            [2500000, '1M-5M'],
            [5000000, '5M-10M'],
            [10000000, '10M-50M'],
            [50000000, '50M+'],
            [100000000, '50M+'],
        ];
    }

    /**
     * @dataProvider aovDataProvider
     */
    public function test_it_maps_aov_to_correct_range(int|float $value, string $expectedRange): void
    {
        self::assertSame($expectedRange, ValueRangeMapper::mapAov($value));
    }

    public static function aovDataProvider(): array
    {
        return [
            [0, '0-50'],
            [25, '0-50'],
            [49.99, '0-50'],
            [50, '50-100'],
            [75, '50-100'],
            [100, '100-250'],
            [150, '100-250'],
            [250, '250-500'],
            [500, '500-1K'],
            [750, '500-1K'],
            [1000, '1K-5K'],
            [2500, '1K-5K'],
            [5000, '5K-10K'],
            [7500, '5K-10K'],
            [10000, '10K-25K'],
            [15000, '10K-25K'],
            [25000, '25K-50K'],
            [35000, '25K-50K'],
            [50000, '50K+'],
            [100000, '50K+'],
            [1000000, '50K+'],
        ];
    }

    /**
     * @dataProvider countDataProvider
     */
    public function test_it_maps_products_count_to_correct_range(int $value, string $expectedRange): void
    {
        self::assertSame($expectedRange, ValueRangeMapper::mapProductsCount($value));
    }

    /**
     * @dataProvider countDataProvider
     */
    public function test_it_maps_variants_count_to_correct_range(int $value, string $expectedRange): void
    {
        self::assertSame($expectedRange, ValueRangeMapper::mapVariantsCount($value));
    }

    /**
     * @dataProvider countDataProvider
     */
    public function test_it_maps_virtual_variants_count_to_correct_range(int $value, string $expectedRange): void
    {
        self::assertSame($expectedRange, ValueRangeMapper::mapVirtualVariantsCount($value));
    }

    public static function countDataProvider(): array
    {
        return [
            [0, '0-100'],
            [50, '0-100'],
            [99, '0-100'],
            [100, '100-1K'],
            [500, '100-1K'],
            [999, '100-1K'],
            [1000, '1K-10K'],
            [5000, '1K-10K'],
            [10000, '10K-100K'],
            [50000, '10K-100K'],
            [100000, '100K-500K'],
            [250000, '100K-500K'],
            [500000, '500K-1M'],
            [750000, '500K-1M'],
            [1000000, '1M-2M'],
            [1500000, '1M-2M'],
            [2000000, '2M+'],
            [5000000, '2M+'],
        ];
    }

    /**
     * @dataProvider customerOrderDataProvider
     */
    public function test_it_maps_customers_count_to_correct_range(int $value, string $expectedRange): void
    {
        self::assertSame($expectedRange, ValueRangeMapper::mapCustomersCount($value));
    }

    /**
     * @dataProvider customerOrderDataProvider
     */
    public function test_it_maps_orders_count_to_correct_range(int $value, string $expectedRange): void
    {
        self::assertSame($expectedRange, ValueRangeMapper::mapOrdersCount($value));
    }

    public static function customerOrderDataProvider(): array
    {
        return [
            [0, '0-100'],
            [50, '0-100'],
            [100, '100-1K'],
            [500, '100-1K'],
            [1000, '1K-10K'],
            [5000, '1K-10K'],
            [10000, '10K-100K'],
            [50000, '10K-100K'],
            [100000, '100K-1M'],
            [500000, '100K-1M'],
            [1000000, '1M+'],
            [5000000, '1M+'],
        ];
    }

    /**
     * @dataProvider avgItemsDataProvider
     */
    public function test_it_maps_avg_items_to_correct_range(int|float $value, string $expectedRange): void
    {
        self::assertSame($expectedRange, ValueRangeMapper::mapAvgItems($value));
    }

    public static function avgItemsDataProvider(): array
    {
        return [
            [0, '0-5'],
            [2.5, '0-5'],
            [4.99, '0-5'],
            [5, '5-10'],
            [7.5, '5-10'],
            [10, '10-20'],
            [15, '10-20'],
            [20, '20+'],
            [50, '20+'],
        ];
    }
}
