<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.processor.catalog_promotion.all_product_variant', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessor')
        ->args([
            service('sylius.repository.product_variant'),
            service('sylius.command_dispatcher.catalog_promotion.batched_apply_on_variants'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface', 'sylius.processor.catalog_promotion.all_product_variant');

    $services->set('sylius.processor.catalog_promotion.clearer', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearer');

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearerInterface', 'sylius.processor.catalog_promotion.clearer');

    $services->set('sylius.processor.catalog_promotion.state', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessor')
        ->args([
            service('sylius.checker.catalog_promotion_eligibility'),
            service('sylius_abstraction.state_machine'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface', 'sylius.processor.catalog_promotion.state');

    $services->set('sylius.processor.catalog_promotion.product', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductCatalogPromotionsProcessor')
        ->args([service('sylius.command_dispatcher.catalog_promotion.batched_apply_on_variants')]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductCatalogPromotionsProcessorInterface', 'sylius.processor.catalog_promotion.product');

    $services->set('sylius.processor.catalog_promotion.product_variant', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductVariantCatalogPromotionsProcessor')
        ->args([service('sylius.command_dispatcher.catalog_promotion.batched_apply_on_variants')]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductVariantCatalogPromotionsProcessorInterface', 'sylius.processor.catalog_promotion.product_variant');

    $services->set('sylius.processor.catalog_promotion.removal', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor')
        ->args([
            service('sylius.repository.catalog_promotion'),
            service('sylius.announcer.catalog_promotion.removal'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessorInterface', 'sylius.processor.catalog_promotion.removal');
};
