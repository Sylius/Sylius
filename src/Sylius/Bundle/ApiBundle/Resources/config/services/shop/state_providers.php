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

use Sylius\Bundle\ApiBundle\StateProvider\Common\Adjustment\CollectionProvider as AdjustmentCollectionProvider;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Channel\CollectionProvider as ChannelCollectionProvider;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\OrderItem\Adjustment\CollectionProvider as OrderItemAdjustmentCollectionProvider;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\Payment\PaymentMethod\CollectionProvider as PaymentMethodCollectionProvider;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Order\Shipment\ShippingMethod\CollectionProvider as ShippingMethodCollectionProvider;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Payment\ItemProvider as PaymentItemProvider;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Payment\PaymentRequest\ItemProvider as PaymentRequestItemProvider;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Product\ProductAttributeValue\CollectionProvider as ProductAttributeValueCollectionProvider;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\Shipment\ItemProvider as ShipmentItemProvider;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\TaxonTree\AbstractTaxonTreeProvider;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\TaxonTree\TaxonTreeBranchProvider;
use Sylius\Bundle\ApiBundle\StateProvider\Shop\TaxonTree\TaxonTreePathProvider;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_api.state_provider.shop.order.adjustment.collection', AdjustmentCollectionProvider::class)
        ->args([
            service('sylius.repository.order'),
            'tokenValue',
        ])
        ->tag('api_platform.state_provider', ['priority' => 10])
    ;

    $services
        ->set('sylius_api.state_provider.shop.order.order_item.adjustment.collection', OrderItemAdjustmentCollectionProvider::class)
        ->args([
            service('sylius.repository.order_item'),
            service('sylius.section_resolver.uri_based'),
        ])
        ->tag('api_platform.state_provider', ['priority' => 10])
    ;

    $services
        ->set('sylius_api.state_provider.shop.channel.collection', ChannelCollectionProvider::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('api_platform.state_provider', ['priority' => 10])
    ;

    $services
        ->set('sylius_api.state_provider.shop.product.product_attribute_value.collection', ProductAttributeValueCollectionProvider::class)
        ->args([
            tagged_iterator('api_platform.doctrine.orm.query_extension.collection'),
            service('sylius.section_resolver.uri_based'),
            service('sylius.repository.product_attribute_value'),
            service('sylius.context.locale'),
            service('sylius.provider.locale.channel_based'),
            '%locale%',
        ])
        ->tag('api_platform.state_provider', ['priority' => 10])
    ;

    $services
        ->set('sylius_api.state_provider.shop.order.shipment.shipping_method.collection', ShippingMethodCollectionProvider::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius.repository.shipment'),
            service('sylius.resolver.shipping_methods'),
        ])
        ->tag('api_platform.state_provider')
    ;

    $services
        ->set('sylius_api.state_provider.shop.order.payment.payment_method.collection', PaymentMethodCollectionProvider::class)
        ->args([
            service('sylius.repository.payment'),
            service('sylius.repository.order'),
            service('sylius.section_resolver.uri_based'),
            service('sylius.resolver.payment_methods'),
        ])
        ->tag('api_platform.state_provider')
    ;

    $services
        ->set('sylius_api.state_provider.shop.shipment.item', ShipmentItemProvider::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
            service('sylius.repository.shipment'),
        ])
        ->tag('api_platform.state_provider', ['priority' => 10])
    ;

    $services
        ->set('sylius_api.state_provider.shop.payment.item', PaymentItemProvider::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius_api.context.user.token_based'),
            service('sylius.repository.payment'),
        ])
        ->tag('api_platform.state_provider', ['priority' => 10])
    ;

    $services
        ->set('sylius_api.state_provider.shop.payment.payment_request.item', PaymentRequestItemProvider::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius.repository.payment_request'),
            service('sylius.checker.finalized_payment_request'),
            service('sylius_api.context.user.token_based'),
        ])
        ->tag('api_platform.state_provider', ['priority' => 10])
    ;

    $services
        ->set('sylius_api.state_provider.shop.taxon_tree.abstract', AbstractTaxonTreeProvider::class)
        ->args([
            service('sylius.provider.taxon_tree'),
            service('sylius.section_resolver.uri_based'),
        ])
        ->abstract()
    ;

    $services
        ->set('sylius_api.state_provider.shop.taxon_tree.branch', TaxonTreeBranchProvider::class)
        ->parent('sylius_api.state_provider.shop.taxon_tree.abstract')
        ->tag('api_platform.state_provider', ['priority' => 10])
    ;

    $services
        ->set('sylius_api.state_provider.shop.taxon_tree.path', TaxonTreePathProvider::class)
        ->parent('sylius_api.state_provider.shop.taxon_tree.abstract')
        ->tag('api_platform.state_provider', ['priority' => 10])
    ;
};
