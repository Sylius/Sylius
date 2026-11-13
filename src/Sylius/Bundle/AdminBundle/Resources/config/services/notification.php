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

use Sylius\Bundle\AdminBundle\Notification\CompositeNotificationProvider;
use Sylius\Bundle\AdminBundle\Notification\HubNotificationProvider;
use Sylius\Bundle\AdminBundle\Notification\NotificationProviderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_admin.provider.notification', CompositeNotificationProvider::class)
        ->args([tagged_iterator('sylius_admin.notification')])
    ;
    $services->alias(NotificationProviderInterface::class, 'sylius_admin.provider.notification');

    $services->alias('sylius_admin.provider.notification.composite', 'sylius_admin.provider.notification');

    $services
        ->set('sylius_admin.provider.notification.hub', HubNotificationProvider::class)
        ->args([
            service('sylius_admin.http_client'),
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
        ->tag('sylius_admin.notification')
    ;
};
