<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->defaults()
        ->public();

    $services->set('sylius.listener.catalog_promotion', 'Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\CatalogPromotionEventListener')
        ->args([service('sylius.announcer.catalog_promotion')])
        ->tag('kernel.event_listener', ['event' => 'sylius.catalog_promotion.post_create', 'method' => 'handleCatalogPromotionCreatedEvent'])
        ->tag('kernel.event_listener', ['event' => 'sylius.catalog_promotion.post_update', 'method' => 'handleCatalogPromotionUpdatedEvent']);

    $services->set('sylius.listener.catalog_promotion.product', 'Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\ProductEventListener')
        ->args([service('sylius.event_bus')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.post_create', 'method' => 'dispatchProductCreatedEvent'])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.post_update', 'method' => 'dispatchProductUpdatedEvent']);

    $services->set('sylius.listener.catalog_promotion.product_variant', 'Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\ProductVariantEventListener')
        ->args([service('sylius.event_bus')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product_variant.post_create', 'method' => 'dispatchProductVariantCreatedEvent'])
        ->tag('kernel.event_listener', ['event' => 'sylius.product_variant.post_update', 'method' => 'dispatchProductVariantUpdatedEvent']);

    $services->set('sylius.listener.catalog_promotion.created', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionCreatedListener')
        ->args([
            service('sylius.processor.catalog_promotion.all_product_variant'),
            service('sylius.repository.catalog_promotion'),
            service('doctrine.orm.entity_manager'),
            service('sylius.command_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius.listener.catalog_promotion.updated', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionUpdatedListener')
        ->args([
            service('sylius.processor.catalog_promotion.all_product_variant'),
            service('sylius.repository.catalog_promotion'),
            service('doctrine.orm.entity_manager'),
            service('sylius.command_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius.listener.catalog_promotion.ended', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionEndedListener')
        ->args([
            service('sylius.processor.catalog_promotion.all_product_variant'),
            service('sylius.repository.catalog_promotion'),
            service('doctrine.orm.entity_manager'),
            service('sylius.command_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius.listener.catalog_promotion.state_changed', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionStateChangedListener')
        ->args([service('sylius.command_bus')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius.listener.catalog_promotion.product_created', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductCreatedListener')
        ->args([
            service('sylius.repository.product'),
            service('sylius.processor.catalog_promotion.product'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius.listener.catalog_promotion.product_updated', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductUpdatedListener')
        ->args([
            service('sylius.repository.product'),
            service('sylius.processor.catalog_promotion.product'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius.listener.catalog_promotion.product_variant_created', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductVariantCreatedListener')
        ->args([
            service('sylius.repository.product_variant'),
            service('sylius.processor.catalog_promotion.product_variant'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);

    $services->set('sylius.listener.catalog_promotion.product_variant_updated', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductVariantUpdatedListener')
        ->args([
            service('sylius.repository.product_variant'),
            service('sylius.processor.catalog_promotion.product_variant'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus']);
};
