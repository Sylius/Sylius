<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.applicator.catalog_promotion', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicator')
        ->args([
            service('sylius.applicator.catalog_promotion.action_based_discount'),
            service('sylius.checker.catalog_promotion.product_variant_for_catalog_promotion_eligibility'),
            service('sylius.checker.catalog_promotion_eligibility'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicatorInterface', 'sylius.applicator.catalog_promotion');

    $services->set('sylius.applicator.catalog_promotion.action_based_discount', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\ActionBasedDiscountApplicator')
        ->args([
            service('sylius.calculator.catalog_promotion.price'),
            tagged_iterator('sylius.catalog_promotion.applicator_criteria'),
        ]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\ActionBasedDiscountApplicatorInterface', 'sylius.applicator.catalog_promotion.action_based_discount');
};
