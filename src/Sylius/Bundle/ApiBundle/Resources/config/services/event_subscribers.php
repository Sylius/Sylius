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

use Sylius\Bundle\ApiBundle\EventSubscriber\AttributeEventSubscriber;
use Sylius\Bundle\ApiBundle\EventSubscriber\CatalogPromotionEventSubscriber;
use Sylius\Bundle\ApiBundle\EventSubscriber\KernelRequestEventSubscriber;
use Sylius\Bundle\ApiBundle\EventSubscriber\PaymentRequestEventSubscriber;
use Sylius\Bundle\ApiBundle\EventSubscriber\ProductDeletionEventSubscriber;
use Sylius\Bundle\ApiBundle\EventSubscriber\ProductSlugEventSubscriber;
use Sylius\Bundle\ApiBundle\EventSubscriber\ProductVariantEventSubscriber;
use Sylius\Bundle\ApiBundle\EventSubscriber\TaxonDeletionEventSubscriber;
use Sylius\Bundle\ApiBundle\EventSubscriber\TaxonSlugEventSubscriber;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius_api.event_subscriber.product_variant', ProductVariantEventSubscriber::class)
        ->args([service('sylius.event_bus')])
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius_api.event_subscriber.catalog_promotion', CatalogPromotionEventSubscriber::class)
        ->args([service('sylius.announcer.catalog_promotion')])
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius_api.event_subscriber.kernel_request', KernelRequestEventSubscriber::class)
        ->args([
            '%sylius_api.enabled%',
            '%sylius.security.api_route%',
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius_api.event_subscriber.payment_request', PaymentRequestEventSubscriber::class)
        ->args([service('sylius.announcer.payment_request')])
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius_api.event_subscriber.product_deletion', ProductDeletionEventSubscriber::class)
        ->args([service('sylius.checker.promotion.product_in_promotion_rule')])
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius_api.event_subscriber.product_slug', ProductSlugEventSubscriber::class)
        ->args([service('sylius.generator.slug')])
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius_api.event_subscriber.taxon_deletion', TaxonDeletionEventSubscriber::class)
        ->args([
            service('sylius.repository.channel'),
            service('sylius.checker.promotion.taxon_in_promotion_rule'),
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius_api.event_subscriber.taxon_slug', TaxonSlugEventSubscriber::class)
        ->args([service('sylius.generator.taxon_slug')])
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius_api.event_subscriber.attribute', AttributeEventSubscriber::class)
        ->args([service('sylius.registry.attribute_type')])
        ->tag('kernel.event_subscriber')
    ;
};
