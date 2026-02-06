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

use Sylius\Bundle\AddressingBundle\EventListener\ZoneMemberIntegrityListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius.listener.zone_member_integrity', ZoneMemberIntegrityListener::class)
        ->args([
            service('request_stack'),
            service('sylius.checker.zone_deletion'),
            service('sylius.checker.country_provinces_deletion'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.zone.pre_delete', 'method' => 'protectFromRemovingZone'])
        ->tag('kernel.event_listener', ['event' => 'sylius.country.pre_update', 'method' => 'protectFromRemovingProvinceWithinCountry']);
};
