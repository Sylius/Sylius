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

use Sylius\Bundle\CoreBundle\Provider\ChannelBasedDefaultTaxZoneProvider;
use Sylius\Bundle\CoreBundle\Provider\ChannelBasedProductTranslationProvider;
use Sylius\Bundle\CoreBundle\Provider\ChannelBasedProductTranslationProviderInterface;
use Sylius\Bundle\CoreBundle\Provider\CustomerProvider;
use Sylius\Bundle\CoreBundle\Provider\CustomerProviderInterface;
use Sylius\Bundle\CoreBundle\Provider\DeliveryTimeProviderInterface;
use Sylius\Bundle\CoreBundle\Provider\FastestDeliveryTimeProvider;
use Sylius\Component\Core\Provider\TranslationLocaleProvider;
use Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProvider;
use Sylius\Component\Core\Statistics\Provider\BusinessActivitySummaryProviderInterface;
use Sylius\Component\Core\Statistics\Provider\OrdersCount\DayBasedOrdersCountProvider;
use Sylius\Component\Core\Statistics\Provider\OrdersCount\MonthBasedOrdersCountProvider;
use Sylius\Component\Core\Statistics\Provider\OrdersCount\YearBasedOrdersCountProvider;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\DayBasedOrdersTotalProvider;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\MonthBasedOrdersTotalProvider;
use Sylius\Component\Core\Statistics\Provider\OrdersTotals\YearBasedOrdersTotalProvider;
use Sylius\Component\Core\Statistics\Provider\SalesStatisticsProvider;
use Sylius\Component\Core\Statistics\Provider\SalesStatisticsProviderInterface;
use Sylius\Component\Core\Statistics\Provider\StatisticsProvider;
use Sylius\Component\Core\Statistics\Provider\StatisticsProviderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $parameters->set('sylius_core.orders_statistics.cache_expires_after', 1800);

    $services
        ->set('sylius.provider.delivery_time', FastestDeliveryTimeProvider::class)
        ->args([service('sylius.repository.shipping_method')])
    ;
    $services->alias(DeliveryTimeProviderInterface::class, 'sylius.provider.delivery_time');

    $services->set('sylius.provider.channel_based_default_zone', ChannelBasedDefaultTaxZoneProvider::class);

    $services
        ->set('sylius.provider.channel_based_product_translation', ChannelBasedProductTranslationProvider::class)
        ->args([service('sylius.context.locale')])
    ;
    $services->alias(ChannelBasedProductTranslationProviderInterface::class, 'sylius.provider.channel_based_product_translation');

    $services
        ->set('sylius.provider.customer', CustomerProvider::class)
        ->args([
            service('sylius.repository.customer'),
            service('sylius.canonicalizer'),
        ])
    ;
    $services->alias(CustomerProviderInterface::class, 'sylius.provider.customer');

    $services
        ->set('sylius.provider.translation_locale.admin', TranslationLocaleProvider::class)
        ->args([
            service('sylius.provider.locale_collection'),
            '%locale%',
        ])
    ;

    $services
        ->set('sylius.provider.statistics', StatisticsProvider::class)
        ->args([
            service('sylius.provider.statistics.sales'),
            service('sylius.provider.statistics.business_activity_summary'),
        ])
    ;
    $services->alias(StatisticsProviderInterface::class, 'sylius.provider.statistics');

    $services
        ->set('sylius.provider.statistics.business_activity_summary', BusinessActivitySummaryProvider::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.customer'),
        ])
    ;
    $services->alias(BusinessActivitySummaryProviderInterface::class, 'sylius.provider.statistics.business_activity_summary');

    $services
        ->set('sylius.provider.statistics.sales', SalesStatisticsProvider::class)
        ->args([
            service('sylius.registry.statistics.orders_totals_providers'),
            '%sylius_core.orders_statistics.intervals_map%',
            tagged_iterator('sylius.statistics.provider_registry'),
            service('cache.app'),
            '%sylius_core.orders_statistics.cache_expires_after%',
        ])
    ;
    $services->alias(SalesStatisticsProviderInterface::class, 'sylius.provider.statistics.sales');

    $services
        ->set('sylius.provider.statistics.orders_totals.day', DayBasedOrdersTotalProvider::class)
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_totals_provider', ['type' => 'day'])
    ;

    $services
        ->set('sylius.provider.statistics.orders_totals.month', MonthBasedOrdersTotalProvider::class)
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_totals_provider', ['type' => 'month'])
    ;

    $services
        ->set('sylius.provider.statistics.orders_totals.year', YearBasedOrdersTotalProvider::class)
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_totals_provider', ['type' => 'year'])
    ;

    $services
        ->set('sylius.provider.statistics.orders_count.day', DayBasedOrdersCountProvider::class)
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_count_provider', ['type' => 'day'])
    ;

    $services
        ->set('sylius.provider.statistics.orders_count.month', MonthBasedOrdersCountProvider::class)
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_count_provider', ['type' => 'month'])
    ;

    $services
        ->set('sylius.provider.statistics.orders_count.year', YearBasedOrdersCountProvider::class)
        ->args([service('sylius.repository.order')])
        ->tag('sylius.statistics.orders_count_provider', ['type' => 'year'])
    ;
};
