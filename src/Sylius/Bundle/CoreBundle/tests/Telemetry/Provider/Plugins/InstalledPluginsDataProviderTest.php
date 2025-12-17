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

namespace Tests\Sylius\Bundle\CoreBundle\Telemetry\Provider\Plugins;

use Composer\InstalledVersions;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Plugins\InstalledPluginsData;
use Sylius\Bundle\CoreBundle\Telemetry\Provider\Plugins\InstalledPluginsDataProvider;

final class InstalledPluginsDataProviderTest extends TestCase
{
    private string $tempDir;

    protected function setUp(): void
    {
        $this->tempDir = sys_get_temp_dir() . '/sylius_plugin_test_' . uniqid();
        mkdir($this->tempDir);
    }

    protected function tearDown(): void
    {
        if (file_exists($this->tempDir . '/composer.lock')) {
            unlink($this->tempDir . '/composer.lock');
        }
        if (is_dir($this->tempDir)) {
            rmdir($this->tempDir);
        }
    }

    public function test_it_returns_empty_plugins_when_composer_lock_does_not_exist(): void
    {
        $provider = new InstalledPluginsDataProvider($this->tempDir);

        $data = $provider->provide();

        self::assertInstanceOf(InstalledPluginsData::class, $data);
        self::assertSame([], $data->plugins);
    }

    public function test_it_returns_empty_plugins_when_composer_installed_versions_does_not_exist(): void
    {
        if (class_exists(InstalledVersions::class)) {
            self::markTestSkipped('InstalledVersions class exists, cannot test fallback behavior');
        }

        $provider = new InstalledPluginsDataProvider($this->tempDir);

        $data = $provider->provide();

        self::assertInstanceOf(InstalledPluginsData::class, $data);
        self::assertSame([], $data->plugins);
    }

    public function test_installed_plugins_have_required_structure(): void
    {
        if (!class_exists(InstalledVersions::class)) {
            self::markTestSkipped('InstalledVersions class does not exist');
        }

        $syliusPlugins = InstalledVersions::getInstalledPackagesByType('sylius-plugin');

        if (empty($syliusPlugins)) {
            self::markTestSkipped('No sylius plugins installed in test environment');
        }

        $packages = [];
        foreach ($syliusPlugins as $plugin) {
            $packages[] = [
                'name' => $plugin,
                'version' => InstalledVersions::getPrettyVersion($plugin),
                'type' => 'sylius-plugin',
                'notification-url' => 'https://packagist.org/downloads/',
            ];
        }

        $composerLock = ['packages' => $packages];
        file_put_contents($this->tempDir . '/composer.lock', json_encode($composerLock));

        $provider = new InstalledPluginsDataProvider($this->tempDir);
        $data = $provider->provide();

        self::assertInstanceOf(InstalledPluginsData::class, $data);
        foreach ($data->plugins as $plugin) {
            self::assertIsString($plugin->name);
            self::assertIsString($plugin->version);
        }
    }

    public function test_it_shows_only_public_plugins(): void
    {
        if (!class_exists(InstalledVersions::class)) {
            self::markTestSkipped('InstalledVersions class does not exist');
        }

        $syliusPlugins = InstalledVersions::getInstalledPackagesByType('sylius-plugin');

        if (count($syliusPlugins) < 2) {
            self::markTestSkipped('Need at least 2 sylius plugins installed in test environment');
        }

        $firstPlugin = $syliusPlugins[0];
        $secondPlugin = $syliusPlugins[1];

        // Create composer.lock where first plugin is public, second is private
        $composerLock = [
            'packages' => [
                [
                    'name' => $firstPlugin,
                    'version' => InstalledVersions::getPrettyVersion($firstPlugin),
                    'type' => 'sylius-plugin',
                    'notification-url' => 'https://packagist.org/downloads/',
                ],
                [
                    'name' => $secondPlugin,
                    'version' => InstalledVersions::getPrettyVersion($secondPlugin),
                    'type' => 'sylius-plugin',
                    'notification-url' => 'https://private-repo.com/downloads/',
                ],
            ],
        ];

        file_put_contents($this->tempDir . '/composer.lock', json_encode($composerLock));

        $provider = new InstalledPluginsDataProvider($this->tempDir);
        $data = $provider->provide();

        self::assertInstanceOf(InstalledPluginsData::class, $data);

        // Only the public plugin should be returned
        self::assertCount(1, $data->plugins);
        self::assertSame($firstPlugin, $data->plugins[0]->name);

        // Verify the private plugin is not in results
        $pluginNames = array_map(fn ($plugin) => $plugin->name, $data->plugins);
        self::assertNotContains($secondPlugin, $pluginNames);
    }
}
