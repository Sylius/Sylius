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

use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\CreateLogEntryOnPriceChangeObserver;
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\ProcessLowestPricesOnChannelChangeObserver;
use Sylius\Bundle\CoreBundle\PriceHistory\EntityObserver\ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserver;
use Sylius\Bundle\CoreBundle\PriceHistory\EventListener\ChannelPricingLogEntryEventListener;
use Sylius\Bundle\CoreBundle\PriceHistory\EventListener\OnFlushEntityObserverListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.entity_observer.price_history.create_log_entry_on_price_change', CreateLogEntryOnPriceChangeObserver::class)
        ->args([service('sylius.logger.price_history.price_change')])
        ->tag('sylius.entity_observer')
    ;

    $services
        ->set('sylius.entity_observer.price_history.process_lowest_prices_on_channel_change', ProcessLowestPricesOnChannelChangeObserver::class)
        ->args([service('sylius.command_dispatcher.price_history.batched_apply_lowest_price_on_channel_pricings')])
        ->tag('sylius.entity_observer')
    ;

    $services
        ->set('sylius.entity_observer.price_history.process_lowest_prices_on_channel_price_history_config_change', ProcessLowestPricesOnChannelPriceHistoryConfigChangeObserver::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius.command_dispatcher.price_history.batched_apply_lowest_price_on_channel_pricings'),
        ])
        ->tag('sylius.entity_observer')
    ;

    $services
        ->set('sylius.listener.price_history.on_flush_entity_observer', OnFlushEntityObserverListener::class)
        ->args([tagged_iterator('sylius.entity_observer')])
        ->tag('doctrine.event_listener', ['event' => 'onFlush', 'lazy' => true])
    ;

    $services
        ->set('sylius.listener.price_history.channel_pricing_log_entry', ChannelPricingLogEntryEventListener::class)
        ->args([service('sylius.processor.price_history.product_lowest_price_before_discount')])
        ->tag('doctrine.event_listener', ['event' => 'postPersist', 'priority' => 500, 'connection' => 'default'])
    ;
};
