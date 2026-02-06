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

use Sylius\Bundle\ApiBundle\EventHandler\OrderCompletedHandler;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_api.event_handler.order_completed', OrderCompletedHandler::class)
        ->args([
            service('sylius.command_bus'),
            service('sylius.repository.order'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);
};
