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

namespace Tests\Sylius\Bundle\CoreBundle\Telemetry\Collector;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\Collector\TechnicalDataCollector;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Technical\DatabaseData;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Technical\VersionData;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;

final class TechnicalDataCollectorTest extends TestCase
{
    public function test_it_returns_correct_name(): void
    {
        $collector = new TechnicalDataCollector([]);

        self::assertSame('technical', $collector->getName());
    }

    public function test_it_is_enabled_by_default(): void
    {
        $collector = new TechnicalDataCollector([]);

        self::assertTrue($collector->isEnabled());
    }

    public function test_it_can_be_disabled(): void
    {
        $collector = new TechnicalDataCollector([], enabled: false);

        self::assertFalse($collector->isEnabled());
    }

    public function test_it_collects_data_from_multiple_providers(): void
    {
        $versionData = new VersionData('1.12.0', '8.2.0', '6.4.0', '2.15.0', '3.8.0', '3.1.0');
        $databaseData = new DatabaseData('mysql', '8.0');

        $provider1 = $this->createMock(DataProviderInterface::class);
        $provider1->method('provide')->willReturn($versionData);

        $provider2 = $this->createMock(DataProviderInterface::class);
        $provider2->method('provide')->willReturn($databaseData);

        $collector = new TechnicalDataCollector([$provider1, $provider2]);

        $data = $collector->collect();

        self::assertArrayHasKey('sylius_version', $data);
        self::assertArrayHasKey('php_version', $data);
        self::assertArrayHasKey('database', $data);
        self::assertSame('1.12.0', $data['sylius_version']);
        self::assertSame('8.2.0', $data['php_version']);
        self::assertSame('mysql', $data['database']['type']);
    }

    public function test_it_continues_on_provider_error(): void
    {
        $versionData = new VersionData('1.12.0', '8.2.0', '6.4.0', null, null, null);

        $provider1 = $this->createMock(DataProviderInterface::class);
        $provider1->method('provide')->willThrowException(new \RuntimeException('Provider error'));

        $provider2 = $this->createMock(DataProviderInterface::class);
        $provider2->method('provide')->willReturn($versionData);

        $collector = new TechnicalDataCollector([$provider1, $provider2]);

        $data = $collector->collect();

        self::assertArrayHasKey('sylius_version', $data);
        self::assertSame('1.12.0', $data['sylius_version']);
    }

    public function test_it_returns_empty_array_when_no_providers(): void
    {
        $collector = new TechnicalDataCollector([]);

        $data = $collector->collect();

        self::assertSame([], $data);
    }
}
