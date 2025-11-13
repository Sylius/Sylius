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

namespace Sylius\Component\Core\Telemetry\Mapper;

/** @internal */
final class ValueRangeMapper
{
    private const GMV_RANGES = [
        10000 => '0-10K',
        50000 => '10K-50K',
        100000 => '50K-100K',
        500000 => '100K-500K',
        1000000 => '500K-1M',
        5000000 => '1M-5M',
        10000000 => '5M-10M',
        50000000 => '10M-50M',
        \PHP_INT_MAX => '50M+',
    ];

    private const AOV_RANGES = [
        50 => '0-50',
        100 => '50-100',
        250 => '100-250',
        500 => '250-500',
        1000 => '500-1K',
        5000 => '1K-5K',
        10000 => '5K-10K',
        25000 => '10K-25K',
        50000 => '25K-50K',
        \PHP_INT_MAX => '50K+',
    ];

    private const COUNT_RANGES = [
        100 => '0-100',
        1000 => '100-1K',
        10000 => '1K-10K',
        100000 => '10K-100K',
        500000 => '100K-500K',
        1000000 => '500K-1M',
        2000000 => '1M-2M',
        \PHP_INT_MAX => '2M+',
    ];

    private const CUSTOMER_ORDER_PAYMENT_SHIPMENT_RANGES = [
        100 => '0-100',
        1000 => '100-1K',
        10000 => '1K-10K',
        100000 => '10K-100K',
        1000000 => '100K-1M',
        \PHP_INT_MAX => '1M+',
    ];

    private const AVG_ITEMS_RANGES = [
        5 => '0-5',
        10 => '5-10',
        20 => '10-20',
        \PHP_INT_MAX => '20+',
    ];

    public static function mapGmv(int|float $value): string
    {
        return self::mapToRange($value, self::GMV_RANGES);
    }

    public static function mapAov(int|float $value): string
    {
        return self::mapToRange($value, self::AOV_RANGES);
    }

    public static function mapProductsCount(int $value): string
    {
        return self::mapToRange($value, self::COUNT_RANGES);
    }

    public static function mapVariantsCount(int $value): string
    {
        return self::mapToRange($value, self::COUNT_RANGES);
    }

    public static function mapCustomersCount(int $value): string
    {
        return self::mapToRange($value, self::CUSTOMER_ORDER_PAYMENT_SHIPMENT_RANGES);
    }

    public static function mapOrdersCount(int $value): string
    {
        return self::mapToRange($value, self::CUSTOMER_ORDER_PAYMENT_SHIPMENT_RANGES);
    }

    public static function mapAvgItems(int|float $value): string
    {
        return self::mapToRange($value, self::AVG_ITEMS_RANGES);
    }

    public static function mapShipmentsCount(int $value): string
    {
        return self::mapToRange($value, self::CUSTOMER_ORDER_PAYMENT_SHIPMENT_RANGES);
    }

    public static function mapPaymentsCount(int $value): string
    {
        return self::mapToRange($value, self::CUSTOMER_ORDER_PAYMENT_SHIPMENT_RANGES);
    }

    /** @param array<int, string> $ranges */
    private static function mapToRange(int|float $value, array $ranges): string
    {
        foreach ($ranges as $threshold => $label) {
            if ($value < $threshold) {
                return $label;
            }
        }

        return end($ranges);
    }
}
