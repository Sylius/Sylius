<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_admin.listener.shipment_ship', 'Sylius\Bundle\AdminBundle\EventListener\ShipmentShipListener')
        ->args([service('sylius_admin.mailer.shipment_email_manager')])
        ->tag('kernel.event_listener', ['event' => 'sylius.shipment.post_ship', 'method' => 'sendConfirmationEmail']);

    $services->set('sylius_admin.listener.locale', 'Sylius\Bundle\AdminBundle\EventListener\LocaleListener')
        ->args([service('sylius.checker.locale_usage')])
        ->tag('kernel.event_listener', ['event' => 'sylius.locale.pre_delete', 'method' => 'preDelete']);

    $services->set('sylius_admin.listener.resource_delete_exception', 'Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteExceptionListener')
        ->args([
            service('router'),
            service('request_stack'),
        ])
        ->tag('kernel.event_listener', ['event' => 'kernel.exception', 'method' => 'onResourceDeleteException']);

    $services->set('sylius_admin.listener.resource_delete', 'Sylius\Bundle\AdminBundle\EventListener\ResourceDeleteListener')
        ->tag('kernel.event_listener', ['event' => 'kernel.exception', 'method' => 'onResourceDelete']);

    $services->set('sylius_admin.event_subscriber.admin_section_cache_control', 'Sylius\Bundle\AdminBundle\EventListener\AdminSectionCacheControlSubscriber')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('kernel.event_subscriber', ['event' => 'kernel.response']);

    $services->set('sylius_admin.event_subscriber.admin_filter', 'Sylius\Bundle\AdminBundle\EventListener\AdminFilterSubscriber')
        ->args([service('sylius.grid.filter_storage')])
        ->tag('kernel.event_subscriber', ['event' => 'kernel.request']);
};
