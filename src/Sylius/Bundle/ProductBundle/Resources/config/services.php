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

use Sylius\Bundle\ProductBundle\EventListener\SelectProductAttributeChoiceRemoveListener;
use Sylius\Component\Product\Checker\ProductVariantsParityChecker;
use Sylius\Component\Product\Checker\ProductVariantsParityCheckerInterface;
use Sylius\Component\Product\Factory\ProductFactory;
use Sylius\Component\Product\Factory\ProductFactoryInterface;
use Sylius\Component\Product\Factory\ProductVariantFactory;
use Sylius\Component\Product\Factory\ProductVariantFactoryInterface;
use Sylius\Component\Product\Generator\ProductVariantGenerator;
use Sylius\Component\Product\Generator\ProductVariantGeneratorInterface;
use Sylius\Component\Product\Generator\SlugGenerator;
use Sylius\Component\Product\Generator\SlugGeneratorInterface;
use Sylius\Component\Product\Resolver\AvailableProductOptionValuesResolver;
use Sylius\Component\Product\Resolver\AvailableProductOptionValuesResolverInterface;
use Sylius\Component\Product\Resolver\CompositeProductVariantResolver;
use Sylius\Component\Product\Resolver\DefaultProductVariantResolver;
use Sylius\Component\Product\Resolver\ProductVariantResolverInterface;

return static function (ContainerConfigurator $container) {
    $container->import('services/**/*.php');

    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.custom_factory.product_variant', ProductVariantFactory::class)
        ->decorate('sylius.factory.product_variant', null, 256)
        ->args([service('sylius.custom_factory.product_variant.inner')])
        ->private()
    ;

    $services->alias(ProductVariantFactoryInterface::class, 'sylius.factory.product_variant');

    $services
        ->set('sylius.custom_factory.product', ProductFactory::class)
        ->decorate('sylius.factory.product', null, 256)
        ->args([
            service('sylius.custom_factory.product.inner'),
            service('sylius.factory.product_variant'),
        ])
        ->private()
    ;

    $services->alias(ProductFactoryInterface::class, 'sylius.factory.product');

    $services
        ->set('sylius.generator.product_variant', ProductVariantGenerator::class)
        ->args([
            service('sylius.factory.product_variant'),
            service('sylius.checker.product_variants_parity'),
        ])
    ;
    $services->alias(ProductVariantGeneratorInterface::class, 'sylius.generator.product_variant');

    $services->set('sylius.checker.product_variants_parity', ProductVariantsParityChecker::class);
    $services->alias(ProductVariantsParityCheckerInterface::class, 'sylius.checker.product_variants_parity');

    $services
        ->set('sylius.generator.slug', SlugGenerator::class)
        ->args([service('slugger')])
    ;
    $services->alias(SlugGeneratorInterface::class, 'sylius.generator.slug');

    $services
        ->set('sylius.resolver.product_variant', CompositeProductVariantResolver::class)
        ->args([tagged_iterator('sylius.product_variant_resolver')])
    ;
    $services->alias(ProductVariantResolverInterface::class, 'sylius.resolver.product_variant');

    $services->alias('sylius.resolver.product_variant.composite', 'sylius.resolver.product_variant');

    $services
        ->set('sylius.resolver.product_variant.default', DefaultProductVariantResolver::class)
        ->args([service('sylius.repository.product_variant')])
        ->tag('sylius.product_variant_resolver', ['priority' => -999])
    ;

    $services
        ->set('sylius.listener.select_product_attribute_choice_remove', SelectProductAttributeChoiceRemoveListener::class)
        ->args(['%sylius.model.product_attribute_value.class%'])
        ->tag('doctrine.event_listener', ['event' => 'postUpdate', 'lazy' => true])
    ;

    $services->set('sylius.resolver.available_product_option_values', AvailableProductOptionValuesResolver::class);
    $services->alias(AvailableProductOptionValuesResolverInterface::class, 'sylius.resolver.available_product_option_values');
};
