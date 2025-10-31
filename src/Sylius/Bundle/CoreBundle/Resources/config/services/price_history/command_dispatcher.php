<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.command_dispatcher.price_history.batched_apply_lowest_price_on_channel_pricings', 'Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\BatchedApplyLowestPriceOnChannelPricingsCommandDispatcher')
        ->args([
            service('sylius.repository.channel_pricing'),
            service('sylius.command_bus'),
            '%sylius_core.price_history.batch_size%',
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\PriceHistory\CommandDispatcher\ApplyLowestPriceOnChannelPricingsCommandDispatcherInterface', 'sylius.command_dispatcher.price_history.batched_apply_lowest_price_on_channel_pricings');
};
