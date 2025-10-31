<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_admin.provider.notification', 'Sylius\Bundle\AdminBundle\Notification\CompositeNotificationProvider')
        ->args([tagged_iterator('sylius_admin.notification')]);

    $services->alias('Sylius\Bundle\AdminBundle\Notification\NotificationProviderInterface', 'sylius_admin.provider.notification');

    $services->alias('sylius_admin.provider.notification.composite', 'sylius_admin.provider.notification');

    $services->set('sylius_admin.provider.notification.hub', 'Sylius\Bundle\AdminBundle\Notification\HubNotificationProvider')
        ->args([
            service('sylius.http_client'),
            service('request_stack'),
            service('Psr\Http\Message\RequestFactoryInterface'),
            service('Psr\Http\Message\StreamFactoryInterface'),
            service('cache.app'),
            service('clock'),
            '%sylius.admin.notification.uri%',
            '%kernel.environment%',
            '%sylius.admin.notification.hub_enabled%',
            '%sylius.admin.notification.frequency%',
        ])
        ->tag('sylius_admin.notification');
};
