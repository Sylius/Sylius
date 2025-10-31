<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.entity_observer.price_history.create_log_entry_on_price_change', 'Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\CreateLogEntryOnPriceChangeObserver')
        ->args([service('sylius.logger.price_history.price_change')])
        ->tag('sylius.entity_observer');

    $services->set('sylius.entity_observer.price_history.process_lowest_prices_on_channel_change', 'Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\ProcessLowestPricesOnChannelChangeObserver')
        ->args([service('sylius.command_dispatcher.price_history.batched_apply_lowest_price_on_channel_pricings')])
        ->tag('sylius.entity_observer');

    $services->set('sylius.entity_observer.price_history.process_lowest_prices_on_channel_price_history_config_change', 'Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserver')
        ->args([
            service('sylius.repository.channel'),
            service('sylius.command_dispatcher.price_history.batched_apply_lowest_price_on_channel_pricings'),
        ])
        ->tag('sylius.entity_observer');

    $services->set('sylius.listener.price_history.on_flush_entity_observer', 'Sylius\Bundle\CoreBundle\PriceHistory\EventListener\OnFlushEntityObserverListener')
        ->args([tagged_iterator('sylius.entity_observer')])
        ->tag('doctrine.event_listener', ['event' => 'onFlush', 'lazy' => true]);

    $services->set('sylius.listener.price_history.channel_pricing_log_entry', 'Sylius\Bundle\CoreBundle\PriceHistory\EventListener\ChannelPricingLogEntryEventListener')
        ->args([service('sylius.processor.price_history.product_lowest_price_before_discount')])
        ->tag('doctrine.event_listener', ['event' => 'postPersist', 'priority' => 500, 'connection' => 'default']);
};
