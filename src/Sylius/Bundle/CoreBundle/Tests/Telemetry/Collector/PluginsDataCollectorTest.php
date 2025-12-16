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

namespace Sylius\Bundle\CoreBundle\Tests\Telemetry\Collector;

use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\Collector\PluginsDataCollector;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Plugins\InstalledPluginsData;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Plugins\PluginData;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;

final class PluginsDataCollectorTest extends TestCase
{
    public function test_it_returns_correct_name(): void
    {
        $collector = new PluginsDataCollector([]);

        self::assertSame('plugins', $collector->getName());
    }

    public function test_it_is_enabled_by_default(): void
    {
        $collector = new PluginsDataCollector([]);

        self::assertTrue($collector->isEnabled());
    }

    public function test_it_can_be_disabled(): void
    {
        $collector = new PluginsDataCollector([], enabled: false);

        self::assertFalse($collector->isEnabled());
    }

    public function test_it_collects_plugins_data_from_providers(): void
    {
        $pluginsData = new InstalledPluginsData(
            new PluginData('sylius/refund-plugin', '1.0.0'),
        );

        $provider = $this->createMock(DataProviderInterface::class);
        $provider->method('provide')->willReturn($pluginsData);

        $collector = new PluginsDataCollector([$provider]);

        $data = $collector->collect();

        self::assertIsArray($data);
        self::assertCount(1, $data);
        self::assertSame('sylius/refund-plugin', $data[0]['name']);
        self::assertSame('1.0.0', $data[0]['version']);
    }

    public function test_it_returns_empty_array_when_no_providers(): void
    {
        $collector = new PluginsDataCollector([]);

        $data = $collector->collect();

        self::assertSame([], $data);
    }

    public function test_it_continues_on_provider_error(): void
    {
        $pluginsData = new InstalledPluginsData();

        $failingProvider = $this->createMock(DataProviderInterface::class);
        $failingProvider->method('provide')->willThrowException(new \RuntimeException('Provider error'));

        $workingProvider = $this->createMock(DataProviderInterface::class);
        $workingProvider->method('provide')->willReturn($pluginsData);

        $collector = new PluginsDataCollector([$failingProvider, $workingProvider]);

        $data = $collector->collect();

        self::assertIsArray($data);
    }

    public function test_it_merges_data_from_multiple_providers(): void
    {
        $pluginsData1 = new InstalledPluginsData(
            new PluginData('sylius/plugin1', '1.0.0'),
        );
        $pluginsData2 = new InstalledPluginsData(
            new PluginData('sylius/plugin2', '2.0.0'),
        );

        $provider1 = $this->createMock(DataProviderInterface::class);
        $provider1->method('provide')->willReturn($pluginsData1);

        $provider2 = $this->createMock(DataProviderInterface::class);
        $provider2->method('provide')->willReturn($pluginsData2);

        $collector = new PluginsDataCollector([$provider1, $provider2]);

        $data = $collector->collect();

        self::assertCount(2, $data);
        self::assertSame('sylius/plugin1', $data[0]['name']);
        self::assertSame('sylius/plugin2', $data[1]['name']);
    }
}
