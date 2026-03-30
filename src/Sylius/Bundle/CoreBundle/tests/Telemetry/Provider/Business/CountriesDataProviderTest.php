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
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\CountriesData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\CountriesDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;

final class CountriesDataProviderTest extends TestCase
{
    private Connection $connection;

    private CountriesDataProvider $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->connection->method('getDatabasePlatform')->willReturn($this->createMock(AbstractMySQLPlatform::class));
        $this->provider = new CountriesDataProvider($this->connection, new TimeoutRunner());
    }

    public function test_it_provides_countries_from_enabled_channels_with_enabled_countries(): void
    {
        $this->connection->expects(self::once())
            ->method('fetchAllAssociative')
            ->with(self::logicalAnd(
                self::stringContains('co.enabled = true'),
                self::stringContains('c.enabled = true'),
            ))
            ->willReturn([
                ['channel_id' => 1, 'country_code' => 'US'],
                ['channel_id' => 1, 'country_code' => 'CA'],
                ['channel_id' => 2, 'country_code' => 'DE'],
                ['channel_id' => 2, 'country_code' => 'FR'],
            ]);
        $this->connection->expects(self::once())
            ->method('fetchFirstColumn')
            ->with(self::stringContains('WHERE enabled = true'))
            ->willReturn(['US', 'CA', 'DE', 'FR', 'PL', 'GB']);

        $data = $this->provider->provide();

        self::assertInstanceOf(CountriesData::class, $data);
        self::assertCount(4, $data->countries);
        self::assertContains('US', $data->countries);
        self::assertContains('CA', $data->countries);
        self::assertContains('DE', $data->countries);
        self::assertContains('FR', $data->countries);
    }

    public function test_it_merges_countries_from_multiple_channels(): void
    {
        $this->connection->method('fetchAllAssociative')->willReturn([
            ['channel_id' => 1, 'country_code' => 'US'],
            ['channel_id' => 1, 'country_code' => 'CA'],
            ['channel_id' => 2, 'country_code' => 'DE'],
            ['channel_id' => 2, 'country_code' => 'FR'],
            ['channel_id' => 3, 'country_code' => 'PL'],
            ['channel_id' => 3, 'country_code' => 'GB'],
        ]);
        $this->connection->method('fetchFirstColumn')->willReturn(['US', 'CA', 'DE', 'FR', 'PL', 'GB']);

        $data = $this->provider->provide();

        self::assertInstanceOf(CountriesData::class, $data);
        self::assertCount(6, $data->countries);
        self::assertContains('US', $data->countries);
        self::assertContains('CA', $data->countries);
        self::assertContains('DE', $data->countries);
        self::assertContains('FR', $data->countries);
        self::assertContains('PL', $data->countries);
        self::assertContains('GB', $data->countries);
    }

    public function test_it_uses_all_countries_as_fallback_when_channel_has_no_countries(): void
    {
        $this->connection->method('fetchAllAssociative')->willReturn([
            ['channel_id' => 1, 'country_code' => 'US'],
            ['channel_id' => 2, 'country_code' => null],
        ]);
        $this->connection->method('fetchFirstColumn')->willReturn(['US', 'CA', 'DE', 'FR', 'PL']);

        $data = $this->provider->provide();

        self::assertInstanceOf(CountriesData::class, $data);
        self::assertCount(5, $data->countries);
        self::assertContains('US', $data->countries);
        self::assertContains('CA', $data->countries);
        self::assertContains('DE', $data->countries);
        self::assertContains('FR', $data->countries);
        self::assertContains('PL', $data->countries);
    }

    public function test_it_returns_unique_countries_when_channels_share_same_countries(): void
    {
        $this->connection->method('fetchAllAssociative')->willReturn([
            ['channel_id' => 1, 'country_code' => 'US'],
            ['channel_id' => 1, 'country_code' => 'DE'],
            ['channel_id' => 2, 'country_code' => 'US'],
            ['channel_id' => 2, 'country_code' => 'FR'],
        ]);
        $this->connection->method('fetchFirstColumn')->willReturn(['US', 'DE', 'FR']);

        $data = $this->provider->provide();

        self::assertInstanceOf(CountriesData::class, $data);
        self::assertCount(3, $data->countries);
    }

    public function test_it_returns_empty_array_on_database_error(): void
    {
        $this->connection->method('fetchAllAssociative')->willThrowException(new \RuntimeException('Database error'));

        $data = $this->provider->provide();

        self::assertInstanceOf(CountriesData::class, $data);
        self::assertSame([], $data->countries);
    }
}
