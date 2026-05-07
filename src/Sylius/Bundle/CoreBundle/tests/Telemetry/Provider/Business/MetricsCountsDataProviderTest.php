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

namespace Tests\Sylius\Bundle\CoreBundle\Telemetry\Provider\Business;

use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\MetricsCountsData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\MetricsCountsDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;

final class MetricsCountsDataProviderTest extends TestCase
{
    private Connection $connection;

    private MetricsCountsDataProvider $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->connection->method('getDatabasePlatform')->willReturn($this->createMock(AbstractMySQLPlatform::class));
        $this->provider = new MetricsCountsDataProvider($this->connection, new TimeoutRunner());
    }

    public function test_it_provides_all_counts(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '250',
            'products_count' => '42',
            'product_variants_count' => '168',
            'virtual_product_variants_count' => '50',
            'orders_count' => '500',
            'channels_count' => '3',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('100-1K', $data->customersCount);
        self::assertSame('0-100', $data->productsCount);
        self::assertSame('100-1K', $data->productVariantsCount);
        self::assertSame('0-100', $data->virtualProductVariantsCount);
        self::assertSame(500, $data->ordersCount);
        self::assertSame(3, $data->channelsCount);
    }

    public function test_it_maps_large_values_to_correct_ranges(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '1000',
            'products_count' => '5000',
            'product_variants_count' => '20000',
            'virtual_product_variants_count' => '5000',
            'orders_count' => '50000',
            'channels_count' => '5',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('1K-10K', $data->customersCount);
        self::assertSame('1K-10K', $data->productsCount);
        self::assertSame('10K-100K', $data->productVariantsCount);
        self::assertSame('1K-10K', $data->virtualProductVariantsCount);
        self::assertSame(50000, $data->ordersCount);
        self::assertSame(5, $data->channelsCount);
    }

    public function test_it_provides_zero_range_when_all_counts_are_zero(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '0',
            'products_count' => '0',
            'product_variants_count' => '0',
            'virtual_product_variants_count' => '0',
            'orders_count' => '0',
            'channels_count' => '0',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('0-100', $data->customersCount);
        self::assertSame('0-100', $data->productsCount);
        self::assertSame('0-100', $data->productVariantsCount);
        self::assertSame('0-100', $data->virtualProductVariantsCount);
        self::assertSame(0, $data->ordersCount);
        self::assertSame(0, $data->channelsCount);
    }

    public function test_it_provides_large_counts(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '500000',
            'products_count' => '150000',
            'product_variants_count' => '2500000',
            'virtual_product_variants_count' => '250000',
            'orders_count' => '1500000',
            'channels_count' => '10',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('100K-1M', $data->customersCount);
        self::assertSame('100K-500K', $data->productsCount);
        self::assertSame('2M+', $data->productVariantsCount);
        self::assertSame('100K-500K', $data->virtualProductVariantsCount);
        self::assertSame(1500000, $data->ordersCount);
        self::assertSame(10, $data->channelsCount);
    }

    public function test_it_handles_mixed_count_values(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '0',
            'products_count' => '100',
            'product_variants_count' => '0',
            'virtual_product_variants_count' => '0',
            'orders_count' => '5000',
            'channels_count' => '2',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('0-100', $data->customersCount);
        self::assertSame('100-1K', $data->productsCount);
        self::assertSame('0-100', $data->productVariantsCount);
        self::assertSame('0-100', $data->virtualProductVariantsCount);
        self::assertSame(5000, $data->ordersCount);
        self::assertSame(2, $data->channelsCount);
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
        self::assertSame('0-100', $data->virtualProductVariantsCount);
        self::assertSame(0, $data->ordersCount);
        self::assertSame(0, $data->channelsCount);
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
        self::assertSame('0-100', $data->virtualProductVariantsCount);
        self::assertSame(0, $data->ordersCount);
        self::assertSame(0, $data->channelsCount);
    }

    public function test_it_provides_consistent_data_structure(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '123',
            'products_count' => '456',
            'product_variants_count' => '789',
            'virtual_product_variants_count' => '100',
            'orders_count' => '1000',
            'channels_count' => '4',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertIsString($data->customersCount);
        self::assertIsString($data->productsCount);
        self::assertIsString($data->productVariantsCount);
        self::assertIsString($data->virtualProductVariantsCount);
        self::assertIsInt($data->ordersCount);
        self::assertIsInt($data->channelsCount);
    }

    public function test_it_executes_single_query_with_subqueries(): void
    {
        $this->connection->expects(self::once())
            ->method('fetchAssociative')
            ->with(
                self::stringContains('SELECT COUNT(id) FROM sylius_customer'),
                self::callback(function (array $params): bool {
                    return isset($params['completedState']) &&
                        $params['completedState'] === 'completed';
                }),
            )
            ->willReturn([
                'customers_count' => '10',
                'products_count' => '20',
                'product_variants_count' => '30',
                'virtual_product_variants_count' => '5',
                'orders_count' => '40',
                'channels_count' => '1',
            ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertSame('0-100', $data->customersCount);
        self::assertSame('0-100', $data->productsCount);
        self::assertSame('0-100', $data->productVariantsCount);
        self::assertSame('0-100', $data->virtualProductVariantsCount);
        self::assertSame(40, $data->ordersCount);
        self::assertSame(1, $data->channelsCount);
    }

    public function test_it_provides_string_ranges_in_output(): void
    {
        $this->connection->method('fetchAssociative')->willReturn([
            'customers_count' => '100',
            'products_count' => '200',
            'product_variants_count' => '300',
            'virtual_product_variants_count' => '50',
            'orders_count' => '400',
            'channels_count' => '3',
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(MetricsCountsData::class, $data);
        self::assertIsString($data->customersCount);
        self::assertIsString($data->productsCount);
        self::assertIsString($data->productVariantsCount);
        self::assertIsString($data->virtualProductVariantsCount);
        self::assertIsInt($data->ordersCount);
        self::assertIsInt($data->channelsCount);
    }
}
