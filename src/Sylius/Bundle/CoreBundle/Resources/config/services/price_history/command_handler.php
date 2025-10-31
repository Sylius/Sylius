<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.command_handler.price_history.apply_lowest_price_on_channel_pricings', 'Sylius\Bundle\CoreBundle\PriceHistory\CommandHandler\ApplyLowestPriceOnChannelPricingsHandler')
        ->args([
            service('sylius.processor.price_history.product_lowest_price_before_discount'),
            service('sylius.repository.channel_pricing'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.command_bus']);
};
