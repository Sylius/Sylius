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
use Doctrine\DBAL\Platforms\AbstractMySQLPlatform;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\ShippingMethodsData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\ShippingMethodsDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;

final class ShippingMethodsDataProviderTest extends TestCase
{
    private Connection $connection;

    private ShippingMethodsDataProvider $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->connection->method('getDatabasePlatform')->willReturn($this->createMock(AbstractMySQLPlatform::class));
        $this->provider = new ShippingMethodsDataProvider($this->connection, new TimeoutRunner());
    }

    public function test_it_provides_non_archived_shipping_providers_assigned_to_channel(): void
    {
        $this->connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(self::logicalAnd(
                self::stringContains('archived_at IS NULL'),
                self::stringContains('EXISTS (SELECT 1 FROM sylius_shipping_method_channels'),
            ))
            ->willReturn([
                ['code' => 'dhl', 'calculator' => 'dhl_express', 'is_enabled' => 1, 'shipments_count' => 150],
                ['code' => 'flat_rate', 'calculator' => 'flat_rate', 'is_enabled' => 1, 'shipments_count' => 5000],
                ['code' => 'per_item', 'calculator' => 'per_unit_rate', 'is_enabled' => 0, 'shipments_count' => 0],
            ]);

        $data = $this->provider->provide();

        self::assertInstanceOf(ShippingMethodsData::class, $data);
        self::assertCount(3, $data->shippingProviders);

        self::assertSame('dhl', $data->shippingProviders[0]->name);
        self::assertSame('dhl_express', $data->shippingProviders[0]->calculator);
        self::assertSame('100-1K', $data->shippingProviders[0]->shipmentsCount);
        self::assertTrue($data->shippingProviders[0]->enabled);

        self::assertSame('flat_rate', $data->shippingProviders[1]->name);
        self::assertSame('flat_rate', $data->shippingProviders[1]->calculator);
        self::assertSame('1K-10K', $data->shippingProviders[1]->shipmentsCount);
        self::assertTrue($data->shippingProviders[1]->enabled);

        self::assertSame('per_item', $data->shippingProviders[2]->name);
        self::assertSame('per_unit_rate', $data->shippingProviders[2]->calculator);
        self::assertSame('0-100', $data->shippingProviders[2]->shipmentsCount);
        self::assertFalse($data->shippingProviders[2]->enabled);
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
