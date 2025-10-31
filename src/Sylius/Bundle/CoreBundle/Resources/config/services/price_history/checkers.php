<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.checker.product_variant_lowest_price_display', 'Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayChecker');

    $services->alias('Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface', 'sylius.checker.product_variant_lowest_price_display');
};
