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
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\LocalesData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\LocalesDataProvider;

final class LocalesDataProviderTest extends TestCase
{
    private const DEFAULT_LOCALE = 'en_US';

    private Connection $connection;

    private LocalesDataProvider $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->provider = new LocalesDataProvider($this->connection, self::DEFAULT_LOCALE);
    }

    public function test_it_provides_locales_channel_defaults_and_default_locale(): void
    {
        $this->connection->method('fetchFirstColumn')->willReturnOnConsecutiveCalls(
            ['en_US', 'pl_PL', 'de_DE'],
            ['en_US', 'de_DE'],
        );

        $data = $this->provider->provide();

        self::assertInstanceOf(LocalesData::class, $data);
        self::assertCount(3, $data->locales);
        self::assertContains('en_US', $data->locales);
        self::assertContains('pl_PL', $data->locales);
        self::assertContains('de_DE', $data->locales);
        self::assertSame(['en_US', 'de_DE'], $data->channelDefaultLocales);
        self::assertSame('en_US', $data->defaultLocale);
    }

    public function test_it_returns_empty_data_on_error_but_keeps_default_locale(): void
    {
        $this->connection->method('fetchFirstColumn')->willThrowException(new \RuntimeException('Database error'));

        $data = $this->provider->provide();

        self::assertInstanceOf(LocalesData::class, $data);
        self::assertSame([], $data->locales);
        self::assertSame([], $data->channelDefaultLocales);
        self::assertSame('en_US', $data->defaultLocale);
    }

    public function test_it_returns_empty_arrays_when_no_locales(): void
    {
        $this->connection->method('fetchFirstColumn')->willReturn([]);

        $data = $this->provider->provide();

        self::assertInstanceOf(LocalesData::class, $data);
        self::assertSame([], $data->locales);
        self::assertSame([], $data->channelDefaultLocales);
        self::assertSame('en_US', $data->defaultLocale);
    }
}
