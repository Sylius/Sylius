<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->tag('messenger.message_handler', ['bus' => 'sylius.query_bus']);

    $services->set('sylius_api.query_handler.get_customer_statistics', 'Sylius\Bundle\ApiBundle\QueryHandler\GetCustomerStatisticsHandler')
        ->args([
            service('sylius.repository.customer'),
            service('sylius.provider.statistics.customer'),
        ]);

    $services->set('sylius_api.query_handler.get_statistics', 'Sylius\Bundle\ApiBundle\QueryHandler\GetStatisticsHandler')
        ->args([
            service('sylius.provider.statistics'),
            service('sylius.repository.channel'),
        ]);
};
