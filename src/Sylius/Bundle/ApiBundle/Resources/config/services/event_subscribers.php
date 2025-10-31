<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()
        ->public();

    $services->set('sylius_api.event_subscriber.product_variant', 'Sylius\Bundle\ApiBundle\EventSubscriber\ProductVariantEventSubscriber')
        ->args([service('sylius.event_bus')])
        ->tag('kernel.event_subscriber');

    $services->set('sylius_api.event_subscriber.catalog_promotion', 'Sylius\Bundle\ApiBundle\EventSubscriber\CatalogPromotionEventSubscriber')
        ->args([service('sylius.announcer.catalog_promotion')])
        ->tag('kernel.event_subscriber');

    $services->set('sylius_api.event_subscriber.kernel_request', 'Sylius\Bundle\ApiBundle\EventSubscriber\KernelRequestEventSubscriber')
        ->args([
            '%sylius_api.enabled%',
            '%sylius.security.api_route%',
        ])
        ->tag('kernel.event_subscriber');

    $services->set('sylius_api.event_subscriber.payment_request', 'Sylius\Bundle\ApiBundle\EventSubscriber\PaymentRequestEventSubscriber')
        ->args([service('sylius.announcer.payment_request')])
        ->tag('kernel.event_subscriber');

    $services->set('sylius_api.event_subscriber.product_deletion', 'Sylius\Bundle\ApiBundle\EventSubscriber\ProductDeletionEventSubscriber')
        ->args([service('sylius.checker.promotion.product_in_promotion_rule')])
        ->tag('kernel.event_subscriber');

    $services->set('sylius_api.event_subscriber.product_slug', 'Sylius\Bundle\ApiBundle\EventSubscriber\ProductSlugEventSubscriber')
        ->args([service('sylius.generator.slug')])
        ->tag('kernel.event_subscriber');

    $services->set('sylius_api.event_subscriber.taxon_deletion', 'Sylius\Bundle\ApiBundle\EventSubscriber\TaxonDeletionEventSubscriber')
        ->args([
            service('sylius.repository.channel'),
            service('sylius.checker.promotion.taxon_in_promotion_rule'),
        ])
        ->tag('kernel.event_subscriber');

    $services->set('sylius_api.event_subscriber.taxon_slug', 'Sylius\Bundle\ApiBundle\EventSubscriber\TaxonSlugEventSubscriber')
        ->args([service('sylius.generator.taxon_slug')])
        ->tag('kernel.event_subscriber');

    $services->set('sylius_api.event_subscriber.attribute', 'Sylius\Bundle\ApiBundle\EventSubscriber\AttributeEventSubscriber')
        ->args([service('sylius.registry.attribute_type')])
        ->tag('kernel.event_subscriber');
};
