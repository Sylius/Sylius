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

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

use Sylius\Component\Addressing\Checker\CountryProvincesDeletionChecker;
use Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface;
use Sylius\Component\Addressing\Checker\ZoneDeletionChecker;
use Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.checker.zone_deletion', ZoneDeletionChecker::class)
        ->args([service('sylius.repository.zone_member')])
    ;
    $services->alias(ZoneDeletionCheckerInterface::class, 'sylius.checker.zone_deletion');

    $services
        ->set('sylius.checker.country_provinces_deletion', CountryProvincesDeletionChecker::class)
        ->args([
            service('sylius.repository.zone_member'),
            service('sylius.repository.province'),
        ])
    ;
    $services->alias(CountryProvincesDeletionCheckerInterface::class, 'sylius.checker.country_provinces_deletion');
};
