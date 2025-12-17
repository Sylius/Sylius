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

namespace Sylius\Bundle\CoreBundle\Tests\Telemetry\Provider\Business;

use Doctrine\DBAL\Connection;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\OrdersBusinessData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\OrdersBusinessDataProvider;

final class OrdersBusinessDataProviderTest extends TestCase
{
    private Connection $connection;

    private OrdersBusinessDataProvider $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->provider = new OrdersBusinessDataProvider($this->connection);
    }

    public function test_it_provides_all_order_metrics_for_single_currency(): void
    {
        $this->connection->method('fetchAllAssociative')->willReturn([
            [
                'currency_code' => 'USD',
                'order_count' => 150,
                'gmv' => 4500000,
                'avg_items' => 3.5,
                'avg_units' => 5.2,
            ],
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(OrdersBusinessData::class, $data);
        self::assertSame(['USD' => '10K-50K'], $data->gmvMonthly);
        self::assertSame(['USD' => '250-500'], $data->aovMonthly);

        self::assertSame('100-1K', $data->metrics->ordersMonthlyCount);
        self::assertSame('0-5', $data->metrics->ordersMonthlyAvgItems);
        self::assertSame('5-10', $data->metrics->ordersMonthlyAvgItemUnits);
    }

    public function test_it_provides_all_order_metrics_for_multiple_currencies(): void
    {
        // Values in minor units
        $this->connection->method('fetchAllAssociative')->willReturn([
            [
                'currency_code' => 'USD',
                'order_count' => 100,
                'gmv' => 3000000, // 30,000 in minor units, AOV = 300
                'avg_items' => 3.0,
                'avg_units' => 4.5,
            ],
            [
                'currency_code' => 'EUR',
                'order_count' => 50,
                'gmv' => 1500000, // 15,000 in minor units, AOV = 300
                'avg_items' => 2.5,
                'avg_units' => 3.5,
            ],
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(OrdersBusinessData::class, $data);
        self::assertSame(['USD' => '10K-50K', 'EUR' => '10K-50K'], $data->gmvMonthly);
        self::assertSame(['USD' => '250-500', 'EUR' => '250-500'], $data->aovMonthly);

        self::assertSame('100-1K', $data->metrics->ordersMonthlyCount);
        self::assertSame('0-5', $data->metrics->ordersMonthlyAvgItems);
        self::assertSame('0-5', $data->metrics->ordersMonthlyAvgItemUnits);
    }

    public function test_it_provides_empty_data_when_no_orders(): void
    {
        $this->connection->method('fetchAllAssociative')->willReturn([]);

        $data = $this->provider->provide();

        self::assertInstanceOf(OrdersBusinessData::class, $data);
        self::assertSame([], $data->gmvMonthly);
        self::assertSame([], $data->aovMonthly);
        self::assertSame('0-100', $data->metrics->ordersMonthlyCount);
        self::assertSame('0-5', $data->metrics->ordersMonthlyAvgItems);
        self::assertSame('0-5', $data->metrics->ordersMonthlyAvgItemUnits);
    }

    public function test_it_returns_empty_data_on_error(): void
    {
        $this->connection->method('fetchAllAssociative')->willThrowException(new \RuntimeException('Database error'));

        $data = $this->provider->provide();

        self::assertInstanceOf(OrdersBusinessData::class, $data);
        self::assertSame([], $data->gmvMonthly);
        self::assertSame([], $data->aovMonthly);
        self::assertSame('0-100', $data->metrics->ordersMonthlyCount);
        self::assertSame('0-5', $data->metrics->ordersMonthlyAvgItems);
        self::assertSame('0-5', $data->metrics->ordersMonthlyAvgItemUnits);
    }

    public function test_it_rounds_averages_correctly(): void
    {
        // Values in minor units - 1,500 GMV, 10 orders = 150 AOV
        $this->connection->method('fetchAllAssociative')->willReturn([
            [
                'currency_code' => 'USD',
                'order_count' => 10,
                'gmv' => 150000, // 1,500 in minor units
                'avg_items' => 2.4,
                'avg_units' => 3.6,
            ],
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(OrdersBusinessData::class, $data);
        self::assertSame('0-5', $data->metrics->ordersMonthlyAvgItems);
        self::assertSame('0-5', $data->metrics->ordersMonthlyAvgItemUnits);
        self::assertSame(['USD' => '100-250'], $data->aovMonthly);
    }
}
