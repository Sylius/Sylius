<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.checker.zone_deletion', 'Sylius\Component\Addressing\Checker\ZoneDeletionChecker')
        ->args([service('sylius.repository.zone_member')]);

    $services->alias('Sylius\Component\Addressing\Checker\ZoneDeletionCheckerInterface', 'sylius.checker.zone_deletion');

    $services->set('sylius.checker.country_provinces_deletion', 'Sylius\Component\Addressing\Checker\CountryProvincesDeletionChecker')
        ->args([
            service('sylius.repository.zone_member'),
            service('sylius.repository.province'),
        ]);

    $services->alias('Sylius\Component\Addressing\Checker\CountryProvincesDeletionCheckerInterface', 'sylius.checker.country_provinces_deletion');
};
