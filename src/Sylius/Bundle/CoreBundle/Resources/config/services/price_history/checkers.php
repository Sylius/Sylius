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

use Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayChecker;
use Sylius\Component\Core\Checker\ProductVariantLowestPriceDisplayCheckerInterface;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius.checker.product_variant_lowest_price_display', ProductVariantLowestPriceDisplayChecker::class);
    $services->alias(ProductVariantLowestPriceDisplayCheckerInterface::class, 'sylius.checker.product_variant_lowest_price_display');
};
