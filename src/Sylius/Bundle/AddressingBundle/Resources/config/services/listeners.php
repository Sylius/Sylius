<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.listener.zone_member_integrity', 'Sylius\Bundle\AddressingBundle\EventListener\ZoneMemberIntegrityListener')
        ->args([
            service('request_stack'),
            service('sylius.checker.zone_deletion'),
            service('sylius.checker.country_provinces_deletion'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.zone.pre_delete', 'method' => 'protectFromRemovingZone'])
        ->tag('kernel.event_listener', ['event' => 'sylius.country.pre_update', 'method' => 'protectFromRemovingProvinceWithinCountry']);
};
