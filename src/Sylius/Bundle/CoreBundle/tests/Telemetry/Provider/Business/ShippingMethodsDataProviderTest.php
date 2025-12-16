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
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\ShippingMethodsData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\ShippingMethodsDataProvider;

final class ShippingMethodsDataProviderTest extends TestCase
{
    private Connection $connection;
    private ShippingMethodsDataProvider $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->provider = new ShippingMethodsDataProvider($this->connection);
    }

    public function test_it_provides_active_shipping_providers_with_details(): void
    {
        $this->connection->method('fetchAllAssociative')->willReturn([
            ['code' => 'dhl', 'calculator' => 'dhl_express', 'shipments_count' => 150],
            ['code' => 'flat_rate', 'calculator' => 'flat_rate', 'shipments_count' => 5000],
            ['code' => 'per_item', 'calculator' => 'per_unit_rate', 'shipments_count' => 0],
        ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(ShippingMethodsData::class, $data);
        self::assertCount(3, $data->shippingProviders);

        self::assertSame('dhl', $data->shippingProviders[0]->name);
        self::assertSame('dhl_express', $data->shippingProviders[0]->calculator);
        self::assertSame('100-1K', $data->shippingProviders[0]->shipmentsCount);

        self::assertSame('flat_rate', $data->shippingProviders[1]->name);
        self::assertSame('flat_rate', $data->shippingProviders[1]->calculator);
        self::assertSame('1K-10K', $data->shippingProviders[1]->shipmentsCount);

        self::assertSame('per_item', $data->shippingProviders[2]->name);
        self::assertSame('per_unit_rate', $data->shippingProviders[2]->calculator);
        self::assertSame('0-100', $data->shippingProviders[2]->shipmentsCount);
    }

    public function test_it_returns_empty_array_on_error(): void
    {
        $this->connection->method('fetchAllAssociative')->willThrowException(new \RuntimeException('Database error'));

        $data = $this->provider->provide();

        self::assertInstanceOf(ShippingMethodsData::class, $data);
        self::assertSame([], $data->shippingProviders);
    }

    public function test_it_returns_empty_array_when_no_shipping_methods(): void
    {
        $this->connection->method('fetchAllAssociative')->willReturn([]);

        $data = $this->provider->provide();

        self::assertInstanceOf(ShippingMethodsData::class, $data);
        self::assertSame([], $data->shippingProviders);
    }
}
