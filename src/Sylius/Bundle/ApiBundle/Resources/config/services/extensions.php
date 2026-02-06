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

use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Order\StateBasedExtension as AdminOrderStateBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Promotion\PromotionCoupon\PostResultExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common\NonArchivedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common\RestrictingFilterEagerLoadingExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common\TranslationOrderLocaleExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Address\ShopUserBasedExtension as AddressShopUserBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Common\EnabledExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Country\ChannelBasedExtension as CountryChannelBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Currency\ChannelBasedExtension as CurrencyChannelBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Customer\ShopUserBasedExtension as CustomerShopUserBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ExchangeRate\ChannelBasedExtension as ExchangeRateChannelBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Locale\ChannelBasedExtension as LocaleChannelBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order\ChannelBasedExtension as OrderChannelBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order\ShopUserBasedExtension as OrderShopUserBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order\StateBasedExtension as ShopOrderStateBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order\VisitorBasedExtension as OrderVisitorBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\OrderItem\ShopUserBasedExtension as OrderItemShopUserBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\OrderItem\VisitorBasedExtension as OrderItemVisitorBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentMethod\ChannelBasedExtension as PaymentMethodChannelBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentMethod\EnabledExtension as PaymentMethodEnabledExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\ChannelAndLocaleBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\EnabledVariantsExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\EnabledWithinProductAssociationExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\TaxonBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductAssociation\EnabledProductsExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductReview\AcceptedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ShippingMethod\ChannelBasedExtension as ShippingMethodChannelBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ShippingMethod\EnabledExtension as ShippingMethodEnabledExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Taxon\ChannelBasedExtension as TaxonChannelBasedExtension;
use Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Taxon\EnabledChildrenExtension;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_api.doctrine.orm.query_extension.common.non_archived', NonArchivedExtension::class)
        ->tag('api_platform.doctrine.orm.query_extension.collection')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.common.translation_order_locale', TranslationOrderLocaleExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.product_review.accepted', AcceptedExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.address.shop_user_based', AddressShopUserBasedExtension::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.product.channel_and_locale_based', ChannelAndLocaleBasedExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.product.enabled_variants', EnabledVariantsExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.product.taxon_based', TaxonBasedExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.order.channel_based', OrderChannelBasedExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.order_item.visitor_based', OrderItemVisitorBasedExtension::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.order_item.shop_user_based', OrderItemShopUserBasedExtension::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.country.channel_based', CountryChannelBasedExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.taxon.channel_based', TaxonChannelBasedExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.currency.channel_based', CurrencyChannelBasedExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.locale.channel_based', LocaleChannelBasedExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.common.filter_eager_loading', RestrictingFilterEagerLoadingExtension::class)
        ->decorate('api_platform.doctrine.orm.query_extension.filter_eager_loading')
        ->args([
            service('sylius_api.doctrine.orm.query_extension.common.filter_eager_loading.inner'),
            '%sylius_api.filter_eager_loading_extension.restricted_resources%',
        ])
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.order.shop_user_based', OrderShopUserBasedExtension::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.order.visitor_based', OrderVisitorBasedExtension::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.order.state_based', ShopOrderStateBasedExtension::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            '%sylius.api.doctrine.orm.query.extension.shop.order.filter_cart.allowed_operations%',
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.common.enabled', EnabledExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.product_association.enabled_products', EnabledProductsExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.exchange_rate.channel_based', ExchangeRateChannelBasedExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.admin.order.state_based', AdminOrderStateBasedExtension::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            '%sylius_api.order_states_to_filter_out%',
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.admin.promotion.promotion_coupon.post_result', PostResultExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.taxon.enabled_children', EnabledChildrenExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.product.enabled_within_product_association', EnabledWithinProductAssociationExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.shipping_method.channel_based', ShippingMethodChannelBasedExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.shipping_method.enabled', ShippingMethodEnabledExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.payment_method.channel_based', PaymentMethodChannelBasedExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.payment_method.enabled', PaymentMethodEnabledExtension::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;

    $services
        ->set('sylius_api.doctrine.orm.query_extension.shop.customer.shop_user_based', CustomerShopUserBasedExtension::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item')
    ;
};
