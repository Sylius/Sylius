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

use Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\CatalogPromotionEventListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\ProductEventListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\EventListener\ProductVariantEventListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionCreatedListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionEndedListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionStateChangedListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\CatalogPromotionUpdatedListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductCreatedListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductUpdatedListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductVariantCreatedListener;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Listener\ProductVariantUpdatedListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.listener.catalog_promotion', CatalogPromotionEventListener::class)
        ->args([service('sylius.announcer.catalog_promotion')])
        ->tag('kernel.event_listener', ['event' => 'sylius.catalog_promotion.post_create', 'method' => 'handleCatalogPromotionCreatedEvent'])
        ->tag('kernel.event_listener', ['event' => 'sylius.catalog_promotion.post_update', 'method' => 'handleCatalogPromotionUpdatedEvent'])
    ;

    $services
        ->set('sylius.listener.catalog_promotion.product', ProductEventListener::class)
        ->args([service('sylius.event_bus')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.post_create', 'method' => 'dispatchProductCreatedEvent'])
        ->tag('kernel.event_listener', ['event' => 'sylius.product.post_update', 'method' => 'dispatchProductUpdatedEvent'])
    ;

    $services
        ->set('sylius.listener.catalog_promotion.product_variant', ProductVariantEventListener::class)
        ->args([service('sylius.event_bus')])
        ->tag('kernel.event_listener', ['event' => 'sylius.product_variant.post_create', 'method' => 'dispatchProductVariantCreatedEvent'])
        ->tag('kernel.event_listener', ['event' => 'sylius.product_variant.post_update', 'method' => 'dispatchProductVariantUpdatedEvent'])
    ;

    $services
        ->set('sylius.listener.catalog_promotion.created', CatalogPromotionCreatedListener::class)
        ->args([
            service('sylius.processor.catalog_promotion.all_product_variant'),
            service('sylius.repository.catalog_promotion'),
            service('doctrine.orm.entity_manager'),
            service('sylius.command_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus'])
    ;

    $services
        ->set('sylius.listener.catalog_promotion.updated', CatalogPromotionUpdatedListener::class)
        ->args([
            service('sylius.processor.catalog_promotion.all_product_variant'),
            service('sylius.repository.catalog_promotion'),
            service('doctrine.orm.entity_manager'),
            service('sylius.command_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus'])
    ;

    $services
        ->set('sylius.listener.catalog_promotion.ended', CatalogPromotionEndedListener::class)
        ->args([
            service('sylius.processor.catalog_promotion.all_product_variant'),
            service('sylius.repository.catalog_promotion'),
            service('doctrine.orm.entity_manager'),
            service('sylius.command_bus'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus'])
    ;

    $services
        ->set('sylius.listener.catalog_promotion.state_changed', CatalogPromotionStateChangedListener::class)
        ->args([service('sylius.command_bus')])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus'])
    ;

    $services
        ->set('sylius.listener.catalog_promotion.product_created', ProductCreatedListener::class)
        ->args([
            service('sylius.repository.product'),
            service('sylius.processor.catalog_promotion.product'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus'])
    ;

    $services
        ->set('sylius.listener.catalog_promotion.product_updated', ProductUpdatedListener::class)
        ->args([
            service('sylius.repository.product'),
            service('sylius.processor.catalog_promotion.product'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus'])
    ;

    $services
        ->set('sylius.listener.catalog_promotion.product_variant_created', ProductVariantCreatedListener::class)
        ->args([
            service('sylius.repository.product_variant'),
            service('sylius.processor.catalog_promotion.product_variant'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus'])
    ;

    $services
        ->set('sylius.listener.catalog_promotion.product_variant_updated', ProductVariantUpdatedListener::class)
        ->args([
            service('sylius.repository.product_variant'),
            service('sylius.processor.catalog_promotion.product_variant'),
            service('doctrine.orm.entity_manager'),
        ])
        ->tag('messenger.message_handler', ['bus' => 'sylius.event_bus'])
    ;
};
