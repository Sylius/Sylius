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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessor;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\AllProductVariantsCatalogPromotionsProcessorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearer;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionClearerInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessor;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionRemovalProcessorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessor;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\CatalogPromotionStateProcessorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductCatalogPromotionsProcessor;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductCatalogPromotionsProcessorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductVariantCatalogPromotionsProcessor;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Processor\ProductVariantCatalogPromotionsProcessorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.processor.catalog_promotion.all_product_variant', AllProductVariantsCatalogPromotionsProcessor::class)
        ->args([
            service('sylius.repository.product_variant'),
            service('sylius.command_dispatcher.catalog_promotion.batched_apply_on_variants'),
            '%sylius_core.catalog_promotions.batch_size%',
        ])
    ;
    $services->alias(AllProductVariantsCatalogPromotionsProcessorInterface::class, 'sylius.processor.catalog_promotion.all_product_variant');

    $services->set('sylius.processor.catalog_promotion.clearer', CatalogPromotionClearer::class);
    $services->alias(CatalogPromotionClearerInterface::class, 'sylius.processor.catalog_promotion.clearer');

    $services
        ->set('sylius.processor.catalog_promotion.state', CatalogPromotionStateProcessor::class)
        ->args([
            service('sylius.checker.catalog_promotion_eligibility'),
            service('sylius_abstraction.state_machine'),
        ])
    ;
    $services->alias(CatalogPromotionStateProcessorInterface::class, 'sylius.processor.catalog_promotion.state');

    $services
        ->set('sylius.processor.catalog_promotion.product', ProductCatalogPromotionsProcessor::class)
        ->args([service('sylius.command_dispatcher.catalog_promotion.batched_apply_on_variants')])
    ;
    $services->alias(ProductCatalogPromotionsProcessorInterface::class, 'sylius.processor.catalog_promotion.product');

    $services
        ->set('sylius.processor.catalog_promotion.product_variant', ProductVariantCatalogPromotionsProcessor::class)
        ->args([service('sylius.command_dispatcher.catalog_promotion.batched_apply_on_variants')])
    ;
    $services->alias(ProductVariantCatalogPromotionsProcessorInterface::class, 'sylius.processor.catalog_promotion.product_variant');

    $services
        ->set('sylius.processor.catalog_promotion.removal', CatalogPromotionRemovalProcessor::class)
        ->args([
            service('sylius.repository.catalog_promotion'),
            service('sylius.announcer.catalog_promotion.removal'),
        ])
    ;
    $services->alias(CatalogPromotionRemovalProcessorInterface::class, 'sylius.processor.catalog_promotion.removal');
};
