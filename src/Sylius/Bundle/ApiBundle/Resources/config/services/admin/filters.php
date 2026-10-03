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

use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ChannelsAwareChannelFilter;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ExchangeRateFilter;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\ProductVariantCatalogPromotionFilter;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\Filter\TranslationOrderNameAndLocaleFilter;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius_api.search_filter.admin.tax_category')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['code' => 'partial', 'name' => 'partial']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.payment')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['state' => 'exact', 'order.channel.code' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.payment')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['createdAt' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.payment_request')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['action' => 'exact', 'method.code' => 'exact', 'state' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.product_attribute')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['code' => 'partial', 'type' => 'exact', 'translations.name' => 'partial']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.boolean_filter.admin.product_attribute')
        ->parent('api_platform.doctrine.orm.boolean_filter')
        ->args([['translatable' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.product_attribute')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['position' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.shipment')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['state' => 'exact', 'order.channel.code' => 'exact', 'method.code' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.exists_filter.admin.shipping_method')
        ->parent('api_platform.doctrine.orm.exists_filter')
        ->args([['archivedAt' => false]])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.shipping_method')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['code' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.product_association')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['owner.code' => 'partial', 'type.code' => 'partial']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.product_taxon')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['position' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.product_image')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['productVariants' => 'exact', 'productVariants.code' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.product')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['code' => 'partial', 'translations.name' => 'partial', 'productTaxons.taxon.code' => 'exact', 'mainTaxon.code' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.product')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['code' => '', 'createdAt' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.product_variant')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['product' => 'exact', 'code' => 'partial', 'translations.name' => 'partial']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.product_review')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['createdAt' => '', 'title' => '', 'rating' => '', 'status' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.product_review')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['reviewSubject' => 'exact', 'status' => 'exact', 'title' => 'partial']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.order')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['channel.code' => '', 'currencyCode' => 'exact', 'customer.id' => 'exact', 'items.productName' => 'exact', 'shipments.method.code' => 'exact', 'items.variant.translations.name' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.channel_pricing_log_entry')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['channelPricing.channelCode' => 'exact', 'channelPricing.productVariant.code' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.date_filter.admin.order')
        ->parent('api_platform.doctrine.orm.date_filter')
        ->args([['checkoutCompletedAt' => 'exclude_null']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.range_filter.admin.order')
        ->parent('api_platform.doctrine.orm.range_filter')
        ->args([['total' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.order')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['number' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.payment_method')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['code' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.payment_method')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['translations.name' => 'partial', 'code' => 'partial']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.boolean_filter.admin.payment_method')
        ->parent('api_platform.doctrine.orm.boolean_filter')
        ->args([['enabled' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.product_association_type')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['code' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.product_association_type')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['translations.name' => 'partial', 'code' => 'partial']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.currency_search_filter.admin.exchange_rate', ExchangeRateFilter::class)
        ->args([service('doctrine')])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.name_with_locale_order_filter.admin.translatable', TranslationOrderNameAndLocaleFilter::class)
        ->args([service('doctrine')])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.catalog_promotion_search_filter.admin.product_variant', ProductVariantCatalogPromotionFilter::class)
        ->args([
            service('api_platform.symfony.iri_converter'),
            service('doctrine'),
        ])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.catalog_promotion')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['name' => 'partial', 'code' => 'partial', 'state' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.boolean_filter.admin.catalog_promotion')
        ->parent('api_platform.doctrine.orm.boolean_filter')
        ->args([['enabled' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.date_filter.admin.catalog_promotion')
        ->parent('api_platform.doctrine.orm.date_filter')
        ->args([['startDate' => 'exclude_null', 'endDate' => 'exclude_null']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.catalog_promotion')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['code' => '', 'name' => '', 'startDate' => '', 'endDate' => '', 'priority' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.channel_search_filter.admin.channel_aware', ChannelsAwareChannelFilter::class)
        ->args([
            service('api_platform.symfony.iri_converter'),
            service('doctrine'),
        ])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.date_filter.admin.tax_rate')
        ->parent('api_platform.doctrine.orm.date_filter')
        ->args([['startDate' => '', 'endDate' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.customer')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['email' => 'partial', 'firstName' => 'partial', 'lastName' => 'partial', 'group.name' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.customer')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['createdAt' => '', 'email' => '', 'firstName' => '', 'lastName' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.promotion_coupon')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['code' => 'exact', 'promotion.code' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.promotion')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['coupons.code' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.promotion')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['priority' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.promotion_coupon')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['code' => '', 'expiresAt' => '', 'usageLimit' => '', 'perCustomerUsageLimit' => '', 'used' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.product_taxon')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['product.code' => 'exact', 'taxon.code' => 'exact']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.product_variant')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['position' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.exists_filter.admin.promotion')
        ->parent('api_platform.doctrine.orm.exists_filter')
        ->args([['archivedAt' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.product_option')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['code' => 'partial', 'translations.name' => 'partial']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.product_option_value')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['position' => '']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.locale')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['code' => 'partial']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.search_filter.admin.customer_group')
        ->parent('api_platform.doctrine.orm.search_filter')
        ->args([['code' => 'partial', 'name' => 'partial']])
        ->tag('api_platform.filter')
    ;

    $services
        ->set('sylius_api.order_filter.admin.customer_group')
        ->parent('api_platform.doctrine.orm.order_filter')
        ->args([['code' => '', 'name' => '']])
        ->tag('api_platform.filter')
    ;
};
