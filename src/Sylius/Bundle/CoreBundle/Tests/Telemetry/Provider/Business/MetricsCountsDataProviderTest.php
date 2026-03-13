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
use Doctrine\DBAL\Platforms\MySqlPlatform;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\MetricsCountsData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\MetricsCountsDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;

final class MetricsCountsDataProviderTest extends TestCase
{
    /** @var Connection|MockObject */
    private $connection;
    /** @var MetricsCountsDataProvider */
    private $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->connection->method('getDatabasePlatform')->willReturn($this->createMock(MySqlPlatform::class));
        $this->provider = new MetricsCountsDataProvider($this->connection, new TimeoutRunner());
    }

    public function test_it_provides_all_counts(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '250',
            'products_count' => '42',
            'product_variants_count' => '168',
            'orders_count' => '500',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('100-1K', $data->customersCount);
        self::assertSame('0-100', $data->productsCount);
        self::assertSame('100-1K', $data->productVariantsCount);
        self::assertSame(500, $data->ordersCount);
    }

    public function test_it_maps_large_values_to_correct_ranges(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '1000',
            'products_count' => '5000',
            'product_variants_count' => '20000',
            'orders_count' => '50000',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('1K-10K', $data->customersCount);
        self::assertSame('1K-10K', $data->productsCount);
        self::assertSame('10K-100K', $data->productVariantsCount);
        self::assertSame(50000, $data->ordersCount);
    }

    public function test_it_provides_zero_range_when_all_counts_are_zero(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '0',
            'products_count' => '0',
            'product_variants_count' => '0',
            'orders_count' => '0',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('0-100', $data->customersCount);
        self::assertSame('0-100', $data->productsCount);
        self::assertSame('0-100', $data->productVariantsCount);
        self::assertSame(0, $data->ordersCount);
    }

    public function test_it_provides_large_counts(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '500000',
            'products_count' => '150000',
            'product_variants_count' => '2500000',
            'orders_count' => '1500000',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('100K-1M', $data->customersCount);
        self::assertSame('100K-500K', $data->productsCount);
        self::assertSame('2M+', $data->productVariantsCount);
        self::assertSame(1500000, $data->ordersCount);
    }

    public function test_it_handles_mixed_count_values(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '0',
            'products_count' => '100',
            'product_variants_count' => '0',
            'orders_count' => '5000',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('0-100', $data->customersCount);
        self::assertSame('100-1K', $data->productsCount);
        self::assertSame('0-100', $data->productVariantsCount);
        self::assertSame(5000, $data->ordersCount);
    }

    public function test_it_returns_zero_range_for_all_counts_on_database_error(): void
    {
        $this->connection->method('fetchAssociative')
            ->willThrowException(new \RuntimeException('Database connection lost'));

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('0-100', $data->customersCount);
        self::assertSame('0-100', $data->productsCount);
        self::assertSame('0-100', $data->productVariantsCount);
        self::assertSame(0, $data->ordersCount);
    }

    public function test_it_returns_zero_range_for_all_counts_on_query_exception(): void
    {
        $this->connection->method('fetchAssociative')
            ->willThrowException(new \Exception('Table does not exist'));

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('0-100', $data->customersCount);
        self::assertSame('0-100', $data->productsCount);
        self::assertSame('0-100', $data->productVariantsCount);
        self::assertSame(0, $data->ordersCount);
    }

    public function test_it_provides_consistent_data_structure(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '123',
            'products_count' => '456',
            'product_variants_count' => '789',
            'orders_count' => '1000',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertIsString($data->customersCount);
        self::assertIsString($data->productsCount);
        self::assertIsString($data->productVariantsCount);
        self::assertIsInt($data->ordersCount);
    }

    public function test_it_executes_single_query_with_subqueries(): void
    {
        $this->connection->expects(self::once())
            ->method('fetchAssociative')
            ->with(self::stringContains('SELECT COUNT(id) FROM sylius_customer'))
            ->willReturn([
                'customers_count' => '10',
                'products_count' => '20',
                'product_variants_count' => '30',
                'orders_count' => '40',
            ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('0-100', $data->customersCount);
        self::assertSame('0-100', $data->productsCount);
        self::assertSame('0-100', $data->productVariantsCount);
        self::assertSame(40, $data->ordersCount);
    }

    public function test_it_provides_string_ranges_in_output(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '100',
            'products_count' => '200',
            'product_variants_count' => '300',
            'orders_count' => '400',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertIsString($data->customersCount);
        self::assertIsString($data->productsCount);
        self::assertIsString($data->productVariantsCount);
        self::assertIsInt($data->ordersCount);
    }
}
