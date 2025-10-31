<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.calculator.catalog_promotion.price', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculator')
        ->args([tagged_iterator('sylius.catalog_promotion.price_calculator')]);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\CatalogPromotionPriceCalculatorInterface', 'sylius.calculator.catalog_promotion.price');

    $services->set('sylius.calculator.catalog_promotion.fixed_discount_price', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator')
        ->tag('sylius.catalog_promotion.price_calculator', ['type' => 'fixed_discount']);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\ActionBasedPriceCalculatorInterface $fixedDiscountPriceCalculator', 'sylius.calculator.catalog_promotion.fixed_discount_price');

    $services->set('sylius.calculator.catalog_promotion.percentage_discount_price', 'Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator')
        ->tag('sylius.catalog_promotion.price_calculator', ['type' => 'percentage_discount']);

    $services->alias('Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\ActionBasedPriceCalculatorInterface $percentageDiscountPriceCalculator', 'sylius.calculator.catalog_promotion.percentage_discount_price');
};
