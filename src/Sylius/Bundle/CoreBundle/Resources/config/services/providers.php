<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius_core.orders_statistics.cache_expires_after', 1800);

    $services->set('sylius.provider.delivery_time', 'Sylius\Bundle\CoreBundle\Provider\FastestDeliveryTimeProvider')
        ->args([service('sylius.repository.shipping_method')]);

    $services->alias('Sylius\Bundle\CoreBundle\Provider\DeliveryTimeProviderInterface', 'sylius.provider.delivery_time');

    $services->set('sylius.provider.channel_based_default_zone', 'Sylius\Bundle\CoreBundle\Provider\ChannelBasedDefaultTaxZoneProvider');

    $services->set('sylius.provider.channel_based_product_translation', 'Sylius\Bundle\CoreBundle\Provider\ChannelBasedProductTranslationProvider')
        ->args([service('sylius.context.locale')]);

    $services->alias('Sylius\Bundle\CoreBundle\Provider\ChannelBasedProductTranslationProviderInterface', 'sylius.provider.channel_based_product_translation');

    $services->set('sylius.provider.customer', 'Sylius\Bundle\CoreBundle\Provider\CustomerProvider')
        ->args([
            service('sylius.repository.customer'),
            service('sylius.canonicalizer'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\Provider\CustomerProviderInterface', 'sylius.provider.customer');

    $services->set('sylius.provider.translation_locale.admin', 'Sylius\Component\Core\Provider\TranslationLocaleProvider')
        ->args([
            service('sylius.provider.locale_collection'),
            '%locale%',
        ]);

    $services->set('sylius.provider.statistics', 'Sylius\Component\Core\Statistics\Provider\StatisticsProvider')
        ->args([
            service('sylius.provider.statistics.sales'),
            service('sylius.provider.statistics.business_activity_summary'),
        ]);

    $services->alias('Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface', 'sylius.provider.statistics');

    $services->set('sylius.provider.statistics.business_activity_summary', 'Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProvider')
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.customer'),
        ]);

    $services->alias('Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProviderInterface', 'sylius.provider.statistics.business_activity_summary');

    $services->set('sylius.provider.statistics.sales', 'Sylius\Component\Core\Statistics\Provider\SalesStatisticsProvider')
        ->args([
            service('sylius.registry.statistics.orders_totals_providers'),
            '%sylius_core.orders_statistics.intervals_map%',
            tagged_iterator('sylius.statistics.provider_registry'),
            service('cache.app'),
            '%sylius_core.orders_statistics.cache_expires_after%',
        ]);

    $services->alias('Sylius\Component\Core\Statistics\Provider\SalesStatisticsProviderInterface', 'sylius.provider.statistics.sales');

    $services->set('sylius.provider.statistics.orders_totals.day', 'Sylius\Component\Core\Statistics\Provider\OrdersTotals\DayBasedOrdersTotalProvider')
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_totals_provider', ['type' => 'day']);

    $services->set('sylius.provider.statistics.orders_totals.month', 'Sylius\Component\Core\Statistics\Provider\OrdersTotals\MonthBasedOrdersTotalProvider')
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_totals_provider', ['type' => 'month']);

    $services->set('sylius.provider.statistics.orders_totals.year', 'Sylius\Component\Core\Statistics\Provider\OrdersTotals\YearBasedOrdersTotalProvider')
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_totals_provider', ['type' => 'year']);

    $services->set('sylius.provider.statistics.orders_count.day', 'Sylius\Component\Core\Statistics\Provider\OrdersCount\DayBasedOrdersCountProvider')
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_count_provider', ['type' => 'day']);

    $services->set('sylius.provider.statistics.orders_count.month', 'Sylius\Component\Core\Statistics\Provider\OrdersCount\MonthBasedOrdersCountProvider')
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_count_provider', ['type' => 'month']);

    $services->set('sylius.provider.statistics.orders_count.year', 'Sylius\Component\Core\Statistics\Provider\OrdersCount\YearBasedOrdersCountProvider')
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_count_provider', ['type' => 'year']);
};
