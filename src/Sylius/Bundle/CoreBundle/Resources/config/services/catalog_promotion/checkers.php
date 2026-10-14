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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\CatalogPromotionEligibilityCheckerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForProductScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\ProductVariantForCatalogPromotionEligibility;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\ProductVariantForCatalogPromotionEligibilityInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.checker.catalog_promotion.product_variant_for_catalog_promotion_eligibility', ProductVariantForCatalogPromotionEligibility::class)
        ->args([tagged_locator('sylius.catalog_promotion.variant_checker', indexAttribute: 'type')])
    ;
    $services->alias(ProductVariantForCatalogPromotionEligibilityInterface::class, 'sylius.checker.catalog_promotion.product_variant_for_catalog_promotion_eligibility');

    $services
        ->set('sylius.checker.catalog_promotion_eligibility', CatalogPromotionEligibilityChecker::class)
        ->args([tagged_iterator('sylius.catalog_promotion.criteria')])
    ;
    $services->alias(CatalogPromotionEligibilityCheckerInterface::class, 'sylius.checker.catalog_promotion_eligibility');

    $services
        ->set('sylius.checker.catalog_promotion.in_for_product_scope_variant', InForProductScopeVariantChecker::class)
        ->tag('sylius.catalog_promotion.variant_checker', ['type' => 'for_products'])
    ;

    $services
        ->set('sylius.checker.catalog_promotion.in_for_taxons_scope_variant', InForTaxonsScopeVariantChecker::class)
        ->args([
            service('sylius.repository.taxon'),
            service('sylius.repository.tree.taxon'),
        ])
        ->tag('sylius.catalog_promotion.variant_checker', ['type' => 'for_taxons'])
    ;

    $services
        ->set('sylius.checker.catalog_promotion.in_for_variants_scope_variant', InForVariantsScopeVariantChecker::class)
        ->tag('sylius.catalog_promotion.variant_checker', ['type' => 'for_variants'])
    ;
};
