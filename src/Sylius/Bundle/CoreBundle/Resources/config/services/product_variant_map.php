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

use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantAppliedPromotionsMapProvider;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantLowestPriceMapProvider;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantOptionsMapProvider;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantOriginalPriceMapProvider;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantPriceMapProvider;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProvider;
use Sylius\Component\Core\Provider\ProductVariantMap\ProductVariantsMapProviderInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.provider.product_variant_map', ProductVariantsMapProvider::class)
        ->args([tagged_iterator('sylius.product_variant_data_map_provider')])
    ;
    $services->alias(ProductVariantsMapProviderInterface::class, 'sylius.provider.product_variant_map');

    $services
        ->set('sylius.provider.product_variant_map.options', ProductVariantOptionsMapProvider::class)
        ->tag('sylius.product_variant_data_map_provider')
    ;

    $services
        ->set('sylius.provider.product_variant_map.price', ProductVariantPriceMapProvider::class)
        ->args([service('sylius.calculator.product_variant_price')])
        ->tag('sylius.product_variant_data_map_provider')
    ;

    $services
        ->set('sylius.provider.product_variant_map.original_price', ProductVariantOriginalPriceMapProvider::class)
        ->args([service('sylius.calculator.product_variant_price')])
        ->tag('sylius.product_variant_data_map_provider')
    ;

    $services
        ->set('sylius.provider.product_variant_map.applied_promotions', ProductVariantAppliedPromotionsMapProvider::class)
        ->tag('sylius.product_variant_data_map_provider')
    ;

    $services
        ->set('sylius.provider.product_variant_map.lowest_price', ProductVariantLowestPriceMapProvider::class)
        ->args([service('sylius.calculator.product_variant_price')])
        ->tag('sylius.product_variant_data_map_provider')
    ;
};
