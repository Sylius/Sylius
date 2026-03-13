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
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Business\LocalesData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Business\LocalesDataProvider;
use Sylius\Bundle\CoreBundle\Telemetry\Query\TimeoutRunner;

final class LocalesDataProviderTest extends TestCase
{
    private const DEFAULT_LOCALE = 'en_US';

    private Connection $connection;

    private LocalesDataProvider $provider;

    protected function setUp(): void
    {
        $this->connection = $this->createMock(Connection::class);
        $this->connection->method('getDatabasePlatform')->willReturn($this->createMock(AbstractMySQLPlatform::class));
        $this->provider = new LocalesDataProvider($this->connection, new TimeoutRunner(), self::DEFAULT_LOCALE);
    }

    public function test_it_provides_locales_channel_defaults_from_enabled_channels(): void
    {
        $this->connection->expects(self::exactly(2))
            ->method('fetchFirstColumn')
            ->willReturnCallback(function (string $sql): array {
                if (str_contains($sql, 'sylius_locale') && !str_contains($sql, 'sylius_channel')) {
                    return ['en_US', 'pl_PL', 'de_DE'];
                }
                self::assertStringContainsString('c.enabled = 1', $sql);

                return ['en_US', 'de_DE'];
            });

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
