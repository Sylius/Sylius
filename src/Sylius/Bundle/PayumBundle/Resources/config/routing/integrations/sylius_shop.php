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

use Composer\InstalledVersions;
use Symfony\Component\Routing\Loader\Configurator\RoutingConfigurator;

return static function (RoutingConfigurator $routes): void {
    $version = InstalledVersions::getVersion('payum/payum-bundle');
    $extension = version_compare($version, '2.7.0', '>=') ? 'yaml' : 'xml';

    $routes->import('@PayumBundle/Resources/config/routing/authorize.' . $extension);
    $routes->import('@PayumBundle/Resources/config/routing/capture.' . $extension);
    $routes->import('@PayumBundle/Resources/config/routing/notify.' . $extension);
};
