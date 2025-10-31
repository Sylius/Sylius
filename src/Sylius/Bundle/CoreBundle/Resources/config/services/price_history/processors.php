<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.processor.price_history.product_lowest_price_before_discount', 'Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessor')
        ->args([
            service('sylius.repository.channel_pricing_log_entry'),
            service('sylius.repository.channel'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\PriceHistory\Processor\ProductLowestPriceBeforeDiscountProcessorInterface', 'sylius.processor.price_history.product_lowest_price_before_discount');
};
