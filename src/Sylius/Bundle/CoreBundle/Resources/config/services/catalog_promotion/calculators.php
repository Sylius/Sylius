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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculatorInterface;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.calculator.catalog_promotion.price', CatalogPromotionPriceCalculator::class)
        ->args([tagged_iterator('sylius.catalog_promotion.price_calculator')])
    ;
    $services->alias(CatalogPromotionPriceCalculatorInterface::class, 'sylius.calculator.catalog_promotion.price');

    $services
        ->set('sylius.calculator.catalog_promotion.fixed_discount_price', FixedDiscountPriceCalculator::class)
        ->tag('sylius.catalog_promotion.price_calculator', ['type' => 'fixed_discount'])
    ;
    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\ActionBasedPriceCalculatorInterface $fixedDiscountPriceCalculator', 'sylius.calculator.catalog_promotion.fixed_discount_price');

    $services
        ->set('sylius.calculator.catalog_promotion.percentage_discount_price', PercentageDiscountPriceCalculator::class)
        ->tag('sylius.catalog_promotion.price_calculator', ['type' => 'percentage_discount'])
    ;
    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\ActionBasedPriceCalculatorInterface $percentageDiscountPriceCalculator', 'sylius.calculator.catalog_promotion.percentage_discount_price');
};
