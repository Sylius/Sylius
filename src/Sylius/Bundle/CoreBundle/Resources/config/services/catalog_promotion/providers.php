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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForProductScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\InForProductScopeVariantCodesProvider;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\InForTaxonsScopeVariantCodesProvider;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\InForVariantsScopeVariantCodesProvider;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\VariantCodesWithAppliedPromotionProvider;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Provider\VariantCodesWithAppliedPromotionProviderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.provider.catalog_promotion.in_for_product_scope_variant_codes', InForProductScopeVariantCodesProvider::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            '%sylius.model.product_variant.class%',
        ])
        ->tag('sylius.catalog_promotion.variant_codes_provider', ['type' => InForProductScopeVariantChecker::TYPE])
    ;

    $services
        ->set('sylius.provider.catalog_promotion.in_for_taxons_scope_variant_codes', InForTaxonsScopeVariantCodesProvider::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            '%sylius.model.product_variant.class%',
            '%sylius.model.product_taxon.class%',
            '%sylius.model.taxon.class%',
        ])
        ->tag('sylius.catalog_promotion.variant_codes_provider', ['type' => InForTaxonsScopeVariantChecker::TYPE])
    ;

    $services
        ->set('sylius.provider.catalog_promotion.in_for_variants_scope_variant_codes', InForVariantsScopeVariantCodesProvider::class)
        ->tag('sylius.catalog_promotion.variant_codes_provider', ['type' => InForVariantsScopeVariantChecker::TYPE])
    ;

    $services
        ->set('sylius.provider.catalog_promotion.variant_codes_with_applied_promotion', VariantCodesWithAppliedPromotionProvider::class)
        ->args([
            service('doctrine.orm.entity_manager'),
            '%sylius.model.product_variant.class%',
            '%sylius.model.channel_pricing.class%',
        ])
    ;
    $services->alias(VariantCodesWithAppliedPromotionProviderInterface::class, 'sylius.provider.catalog_promotion.variant_codes_with_applied_promotion');
};
