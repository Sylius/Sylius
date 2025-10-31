<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.state_provider.shop.order.adjustment.collection', 'Sylius\Bundle\ApiBundle\StateProvider\Common\Adjustment\CollectionProvider')
        ->args([
            service('sylius.repository.order'),
            'tokenValue',
        ])
        ->tag('api_platform.state_provider', ['priority' => 10]);

    $services->set('sylius_api.state_provider.shop.order.order_item.adjustment.collection', 'Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\OrderItem\Adjustment\CollectionProvider')
        ->args([
            service('sylius.repository.order_item'),
            service('sylius.section_resolver.uri_based'),
        ])
        ->tag('api_platform.state_provider', ['priority' => 10]);

    $services->set('sylius_api.state_provider.shop.channel.collection', 'Sylius\Bundle\ApiBundle\StateProvider\Shop\Channel\CollectionProvider')
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.state_provider', ['priority' => 10]);

    $services->set('sylius_api.state_provider.shop.product.product_attribute_value.collection', 'Sylius\Bundle\ApiBundle\StateProvider\Shop\Product\ProductAttributeValue\CollectionProvider')
        ->args([
            tagged_iterator('api_platform.doctrine.orm.query_extension.collection'),
            service('sylius.section_resolver.uri_based'),
            service('sylius.repository.product_attribute_value'),
            service('sylius.context.locale'),
            service('sylius.provider.locale.channel_based'),
            '%locale%',
        ])
        ->tag('api_platform.state_provider', ['priority' => 10]);

    $services->set('sylius_api.state_provider.shop.order.shipment.shipping_method.collection', 'Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\Shipment\ShippingMethod\CollectionProvider')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius.repository.shipment'),
            service('sylius.resolver.shipping_methods'),
        ])
        ->tag('api_platform.state_provider');

    $services->set('sylius_api.state_provider.shop.order.payment.payment_method.collection', 'Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\Payment\PaymentMethod\CollectionProvider')
        ->args([
            service('sylius.repository.payment'),
            service('sylius.repository.order'),
            service('sylius.section_resolver.uri_based'),
            service('sylius.resolver.payment_methods'),
        ])
        ->tag('api_platform.state_provider');

    $services->set('sylius_api.state_provider.shop.shipment.item', 'Sylius\Bundle\ApiBundle\StateProvider\Shop\Shipment\ItemProvider')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
            service('sylius.repository.shipment'),
        ])
        ->tag('api_platform.state_provider', ['priority' => 10]);

    $services->set('sylius_api.state_provider.shop.payment.item', 'Sylius\Bundle\ApiBundle\StateProvider\Shop\Payment\ItemProvider')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
            service('sylius.repository.payment'),
        ])
        ->tag('api_platform.state_provider', ['priority' => 10]);

    $services->set('sylius_api.state_provider.shop.payment.payment_request.item', 'Sylius\Bundle\ApiBundle\StateProvider\Shop\Payment\PaymentRequest\ItemProvider')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius.repository.payment_request'),
            service('sylius.checker.finalized_payment_request'),
        ])
        ->tag('api_platform.state_provider', ['priority' => 10]);

    $services->set('sylius_api.state_provider.shop.taxon_tree.abstract', 'Sylius\Bundle\ApiBundle\StateProvider\Shop\TaxonTree\AbstractTaxonTreeProvider')
        ->abstract()
        ->args([
            service('sylius.provider.taxon_tree'),
            service('sylius.section_resolver.uri_based'),
        ]);

    $services->set('sylius_api.state_provider.shop.taxon_tree.branch', 'Sylius\Bundle\ApiBundle\StateProvider\Shop\TaxonTree\TaxonTreeBranchProvider')
        ->parent('sylius_api.state_provider.shop.taxon_tree.abstract')
        ->tag('api_platform.state_provider', ['priority' => 10]);

    $services->set('sylius_api.state_provider.shop.taxon_tree.path', 'Sylius\Bundle\ApiBundle\StateProvider\Shop\TaxonTree\TaxonTreePathProvider')
        ->parent('sylius_api.state_provider.shop.taxon_tree.abstract')
        ->tag('api_platform.state_provider', ['priority' => 10]);
};
