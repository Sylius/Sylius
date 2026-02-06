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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\ActionBasedDiscountApplicator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\ActionBasedDiscountApplicatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Applicator\CatalogPromotionApplicatorInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.applicator.catalog_promotion', CatalogPromotionApplicator::class)
        ->args([
            service('sylius.applicator.catalog_promotion.action_based_discount'),
            service('sylius.checker.catalog_promotion.product_variant_for_catalog_promotion_eligibility'),
            service('sylius.checker.catalog_promotion_eligibility'),
        ]);

    $services->alias(CatalogPromotionApplicatorInterface::class, 'sylius.applicator.catalog_promotion');

    $services->set('sylius.applicator.catalog_promotion.action_based_discount', ActionBasedDiscountApplicator::class)
        ->args([
            service('sylius.calculator.catalog_promotion.price'),
            tagged_iterator('sylius.catalog_promotion.applicator_criteria'),
        ]);

    $services->alias(ActionBasedDiscountApplicatorInterface::class, 'sylius.applicator.catalog_promotion.action_based_discount');
};
