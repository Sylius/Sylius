<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.checker.catalog_promotion.product_variant_for_catalog_promotion_eligibility', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\ProductVariantForCatalogPromotionEligibility')
        ->args([tagged_locator('sylius.catalog_promotion.variant_checker', indexAttribute: 'type')]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\ProductVariantForCatalogPromotionEligibilityInterface', 'sylius.checker.catalog_promotion.product_variant_for_catalog_promotion_eligibility');

    $services->set('sylius.checker.catalog_promotion_eligibility', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityChecker')
        ->args([tagged_iterator('sylius.catalog_promotion.criteria')]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityCheckerInterface', 'sylius.checker.catalog_promotion_eligibility');

    $services->set('sylius.checker.catalog_promotion.in_for_product_scope_variant', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForProductScopeVariantChecker')
        ->tag('sylius.catalog_promotion.variant_checker', ['type' => 'for_products']);

    $services->set('sylius.checker.catalog_promotion.in_for_taxons_scope_variant', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker')
        ->args([
            service('sylius.repository.taxon'),
            service('sylius.repository.tree.taxon'),
        ])
        ->tag('sylius.catalog_promotion.variant_checker', ['type' => 'for_taxons']);

    $services->set('sylius.checker.catalog_promotion.in_for_variants_scope_variant', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker')
        ->tag('sylius.catalog_promotion.variant_checker', ['type' => 'for_variants']);
};
