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

use Sylius\Bundle\AdminBundle\EventListener\AdminFilterSubscriber;
use Sylius\Bundle\AdminBundle\EventListener\AdminSectionCacheControlSubscriber;
use Sylius\Bundle\AdminBundle\EventListener\LocaleListener;
use Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteExceptionListener;
use Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteListener;
use Sylius\Bundle\AdminBundle\EventListener\ShipmentShipListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius_admin.listener.shipment_ship', ShipmentShipListener::class)
        ->args([service('sylius_admin.mailer.shipment_email_manager')])
        ->tag('kernel.event_listener', ['event' => 'sylius.shipment.post_ship', 'method' => 'sendConfirmationEmail'])
    ;

    $services
        ->set('sylius_admin.listener.locale', LocaleListener::class)
        ->args([service('sylius.checker.locale_usage')])
        ->tag('kernel.event_listener', ['event' => 'sylius.locale.pre_delete', 'method' => 'preDelete'])
    ;

    $services
        ->set('sylius_admin.listener.resource_delete_exception', ResourceDeleteExceptionListener::class)
        ->args([
            service('router'),
            service('request_stack'),
        ])
        ->tag('kernel.event_listener', ['event' => 'kernel.exception', 'method' => 'onResourceDeleteException'])
    ;

    $services
        ->set('sylius_admin.listener.resource_delete', ResourceDeleteListener::class)
        ->tag('kernel.event_listener', ['event' => 'kernel.exception', 'method' => 'onResourceDelete'])
    ;

    $services
        ->set('sylius_admin.event_subscriber.admin_section_cache_control', AdminSectionCacheControlSubscriber::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('kernel.event_subscriber', ['event' => 'kernel.response'])
    ;

    $services
        ->set('sylius_admin.event_subscriber.admin_filter', AdminFilterSubscriber::class)
        ->args([service('sylius.grid.filter_storage')])
        ->tag('kernel.event_subscriber', ['event' => 'kernel.request'])
    ;
};
