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

use Sylius\Bundle\ProductBundle\Validator\ProductVariantCombinationValidator;
use Sylius\Bundle\ProductBundle\Validator\ProductVariantOptionValuesConfigurationValidator;
use Sylius\Bundle\ProductBundle\Validator\UniqueSimpleProductCodeValidator;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.validator.product_variant_combination', ProductVariantCombinationValidator::class)
        ->args([service('sylius.checker.product_variants_parity')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius.validator.product_variant_combination'])
    ;

    $services
        ->set('sylius.validator.product_variant_option_values_configuration', ProductVariantOptionValuesConfigurationValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius.validator.product_variant_option_values_configuration'])
    ;

    $services
        ->set('sylius.validator.unique_simple_product_code', UniqueSimpleProductCodeValidator::class)
        ->args([service('sylius.repository.product_variant')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius.validator.unique_simple_product_code'])
    ;
};
