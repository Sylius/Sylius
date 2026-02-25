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

namespace Sylius\Bundle\CoreBundle\Telemetry\Provider\Plugins;

use Composer\InstalledVersions;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Plugins\InstalledPluginsData;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Plugins\PluginData;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;

/** @internal */
final class InstalledPluginsDataProvider implements DataProviderInterface
{
    /** @var string */
    private $projectDir;

    public function __construct(string $projectDir)
    {
        $this->projectDir = $projectDir;
    }

    public function provide(): TelemetryDataInterface
    {
        if (!class_exists(InstalledVersions::class)) {
            return new InstalledPluginsData();
        }

        $publicPackages = array_flip($this->getPublicPackageNames());
        $syliusPlugins = array_unique(InstalledVersions::getInstalledPackagesByType('sylius-plugin'));

        $plugins = [];
        foreach ($syliusPlugins as $name) {
            if (!isset($publicPackages[$name])) {
                continue;
            }

            $plugins[] = new PluginData(
                $name,
                InstalledVersions::getPrettyVersion($name) ?? 'unknown'
            );
        }

        return new InstalledPluginsData(...$plugins);
    }

    /** @return list<string> */
    private function getPublicPackageNames(): array
    {
        $lockPath = $this->projectDir . '/composer.lock';

        if (!file_exists($lockPath) || !($content = file_get_contents($lockPath))) {
            return [];
        }

        $lock = json_decode($content, true);

        return array_column(
            array_filter(
                $lock['packages'] ?? [],
                fn ($package) => ($package['notification-url'] ?? '') === 'https://packagist.org/downloads/'
            ),
            'name'
        );
    }
}
