<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.doctrine.orm.query_extension.common.non_archived', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common\NonArchivedExtension')
        ->tag('api_platform.doctrine.orm.query_extension.collection');

    $services->set('sylius_api.doctrine.orm.query_extension.common.translation_order_locale', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common\TranslationOrderLocaleExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.product_review.accepted', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductReview\AcceptedExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.address.shop_user_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Address\ShopUserBasedExtension')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.product.channel_and_locale_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\ChannelAndLocaleBasedExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.product.enabled_variants', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\EnabledVariantsExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.product.taxon_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\TaxonBasedExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.order.channel_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order\ChannelBasedExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.order_item.visitor_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\OrderItem\VisitorBasedExtension')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.order_item.shop_user_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\OrderItem\ShopUserBasedExtension')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.country.channel_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Country\ChannelBasedExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.taxon.channel_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Taxon\ChannelBasedExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.currency.channel_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Currency\ChannelBasedExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.locale.channel_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Locale\ChannelBasedExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection');

    $services->set('sylius_api.doctrine.orm.query_extension.common.filter_eager_loading', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Common\RestrictingFilterEagerLoadingExtension')
        ->decorate('api_platform.doctrine.orm.query_extension.filter_eager_loading')
        ->args([
            service('sylius_api.doctrine.orm.query_extension.common.filter_eager_loading.inner'),
            '%sylius_api.filter_eager_loading_extension.restricted_resources%',
        ]);

    $services->set('sylius_api.doctrine.orm.query_extension.shop.order.shop_user_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order\ShopUserBasedExtension')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.order.visitor_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order\VisitorBasedExtension')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.order.state_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Order\StateBasedExtension')
        ->args([
            service('sylius.section_resolver.uri_based'),
            '%sylius.api.doctrine.orm.query.extension.shop.order.filter_cart.allowed_operations%',
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.common.enabled', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Common\EnabledExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.product_association.enabled_products', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ProductAssociation\EnabledProductsExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.exchange_rate.channel_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ExchangeRate\ChannelBasedExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.admin.order.state_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Order\StateBasedExtension')
        ->args([
            service('sylius.section_resolver.uri_based'),
            '%sylius_api.order_states_to_filter_out%',
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.admin.promotion.promotion_coupon.post_result', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Admin\Promotion\PromotionCoupon\PostResultExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.taxon.enabled_children', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Taxon\EnabledChildrenExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.product.enabled_within_product_association', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Product\EnabledWithinProductAssociationExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.shipping_method.channel_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ShippingMethod\ChannelBasedExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.shipping_method.enabled', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\ShippingMethod\EnabledExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.payment_method.channel_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentMethod\ChannelBasedExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.payment_method.enabled', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\PaymentMethod\EnabledExtension')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');

    $services->set('sylius_api.doctrine.orm.query_extension.shop.customer.shop_user_based', 'Sylius\Bundle\ApiBundle\Doctrine\ORM\QueryExtension\Shop\Customer\ShopUserBasedExtension')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.doctrine.orm.query_extension.collection')
        ->tag('api_platform.doctrine.orm.query_extension.item');
};
