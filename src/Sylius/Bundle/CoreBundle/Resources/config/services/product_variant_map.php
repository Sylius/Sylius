<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.provider.product_variant_map', 'Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProvider')
        ->args([tagged_iterator('sylius.product_variant_data_map_provider')]);

    $services->alias('Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface', 'sylius.provider.product_variant_map');

    $services->set('sylius.provider.product_variant_map.options', 'Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantOptionsMapProvider')
        ->tag('sylius.product_variant_data_map_provider');

    $services->set('sylius.provider.product_variant_map.price', 'Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantPriceMapProvider')
        ->args([service('sylius.calculator.product_variant_price')])
        ->tag('sylius.product_variant_data_map_provider');

    $services->set('sylius.provider.product_variant_map.original_price', 'Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantOriginalPriceMapProvider')
        ->args([service('sylius.calculator.product_variant_price')])
        ->tag('sylius.product_variant_data_map_provider');

    $services->set('sylius.provider.product_variant_map.applied_promotions', 'Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantAppliedPromotionsMapProvider')
        ->tag('sylius.product_variant_data_map_provider');

    $services->set('sylius.provider.product_variant_map.lowest_price', 'Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantLowestPriceMapProvider')
        ->args([service('sylius.calculator.product_variant_price')])
        ->tag('sylius.product_variant_data_map_provider');
};
