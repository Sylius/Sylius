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

use Sylius\Bundle\ApiBundle\QueryHandler\GetCustomerStatisticsHandler;
use Sylius\Bundle\ApiBundle\QueryHandler\GetStatisticsHandler;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->tag('messenger.message_handler', ['bus' => 'sylius.query_bus']);

    $services->set('sylius_api.query_handler.get_customer_statistics', GetCustomerStatisticsHandler::class)
        ->args([
            service('sylius.repository.customer'),
            service('sylius.provider.statistics.customer'),
        ]);

    $services->set('sylius_api.query_handler.get_statistics', GetStatisticsHandler::class)
        ->args([
            service('sylius.provider.statistics'),
            service('sylius.repository.channel'),
        ]);
};
