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
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\CurrenciesData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\CurrenciesDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;

final class CurrenciesDataProviderTest extends TestCase
{
    private Connection $connection;

    private CurrenciesDataProvider $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->connection->method('getDatabasePlatform')->willReturn($this->createMock(AbstractMySQLPlatform::class));
        $this->provider = new CurrenciesDataProvider($this->connection, new TimeoutRunner());
    }

    public function test_it_provides_currency_codes(): void
    {
        $this->connection->method('fetchFirstColumn')->willReturn(['USD', 'EUR', 'GBP']);

        $data = $this->provider->provide();

        self::assertInstanceOf(CurrenciesData::class, $data);
        self::assertCount(3, $data->currencies);
        self::assertContains('USD', $data->currencies);
        self::assertContains('EUR', $data->currencies);
        self::assertContains('GBP', $data->currencies);
    }

    public function test_it_returns_empty_array_on_error(): void
    {
        $this->connection->method('fetchFirstColumn')->willThrowException(new \RuntimeException('Database error'));

        $data = $this->provider->provide();

        self::assertInstanceOf(CurrenciesData::class, $data);
        self::assertSame([], $data->currencies);
    }

    public function test_it_returns_empty_array_when_no_currencies(): void
    {
        $this->connection->method('fetchFirstColumn')->willReturn([]);

        $data = $this->provider->provide();

        self::assertInstanceOf(CurrenciesData::class, $data);
        self::assertSame([], $data->currencies);
    }
}
