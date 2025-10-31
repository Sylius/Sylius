<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.logger.price_history.price_change', 'Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLogger')
        ->args([
            service('sylius.factory.channel_pricing_log_entry'),
            service('sylius.manager.channel_pricing_log_entry'),
            service('clock'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\PriceHistory\Logger\PriceChangeLoggerInterface', 'sylius.logger.price_history.price_change');
};
