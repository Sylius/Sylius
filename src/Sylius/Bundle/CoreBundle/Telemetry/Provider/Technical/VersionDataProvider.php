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

namespace Sylius\Bundle\CoreBundle\Telemetry\Provider\Technical;

use Composer\InstalledVersions;
use Sylius\Bundle\CoreBundle\SyliusCoreBundle;
use Sylius\Bundle\CoreBundle\Telemetry\DTO\Technical\VersionData;
use Sylius\Component\Core\Telemetry\DataProvider\DataProviderInterface;
use Sylius\Component\Core\Telemetry\DTO\TelemetryDataInterface;
use Symfony\Component\HttpKernel\Kernel;
use Twig\Environment;

/** @internal */
final class VersionDataProvider implements DataProviderInterface
{
    public function provide(): TelemetryDataInterface
    {
        return new VersionData(
            SyliusCoreBundle::VERSION,
            PHP_VERSION,
            Kernel::VERSION,
            $this->getInstalledPackageVersion('doctrine/orm'),
            Environment::VERSION,
            $this->getInstalledPackageVersion('api-platform/core')
        );
    }

    private function getInstalledPackageVersion(string $package): ?string
    {
        if (!class_exists(InstalledVersions::class)) {
            return null;
        }

        if (!InstalledVersions::isInstalled($package)) {
            return null;
        }

        return InstalledVersions::getPrettyVersion($package);
    }
}
