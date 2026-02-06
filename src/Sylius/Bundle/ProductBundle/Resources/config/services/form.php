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

use Sylius\Bundle\ProductBundle\Form\DataTransformer\ProductsToProductAssociationsTransformer;
use Sylius\Bundle\ProductBundle\Form\EventSubscriber\GenerateProductVariantsSubscriber;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAssociationsType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAssociationType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAssociationTypeChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAssociationTypeTranslationType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAssociationTypeType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeTranslationType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductAttributeValueType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductCodeChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductGenerateVariantsType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionTranslationType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueChoiceType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueTranslationType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductOptionValueType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductTranslationType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantGenerationType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantTranslationType;
use Sylius\Bundle\ProductBundle\Form\Type\ProductVariantType;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $services = $container->services();
    $parameters->set('sylius.form.type.product_association.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_association_type.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_association_type_translation.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_attribute.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_attribute_translation.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_attribute_value.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_translation.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_generate_variants.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_option.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_option_translation.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_option_value.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_option_value_translation.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_variant.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_variant_translation.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_variant_generation.validation_groups', ['sylius']);

    $services->defaults()->public();

    $services
        ->set('sylius.form.type.product_association', ProductAssociationType::class)
        ->args([
            '%sylius.model.product_association.class%',
            '%sylius.form.type.product_association.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_association_type', ProductAssociationTypeType::class)
        ->args([
            '%sylius.model.product_association_type.class%',
            '%sylius.form.type.product_association_type.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_association_type_translation', ProductAssociationTypeTranslationType::class)
        ->args([
            '%sylius.model.product_association_type_translation.class%',
            '%sylius.form.type.product_association_type_translation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_association_type_choice', ProductAssociationTypeChoiceType::class)
        ->args([service('sylius.repository.product_association_type')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_associations', ProductAssociationsType::class)
        ->args([
            service('sylius.repository.product_association_type'),
            service('sylius.form.type.data_transformer.products_to_product_associations'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_attribute', ProductAttributeType::class)
        ->args([
            '%sylius.model.product_attribute.class%',
            '%sylius.form.type.product_attribute.validation_groups%',
            ProductAttributeTranslationType::class,
            service('sylius.form_registry.attribute_type'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_attribute_translation', ProductAttributeTranslationType::class)
        ->args([
            '%sylius.model.product_attribute_translation.class%',
            '%sylius.form.type.product_attribute_translation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_attribute_choice', ProductAttributeChoiceType::class)
        ->args([service('sylius.repository.product_attribute')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_attribute_value', ProductAttributeValueType::class)
        ->args([
            '%sylius.model.product_attribute_value.class%',
            '%sylius.form.type.product_attribute_value.validation_groups%',
            ProductAttributeChoiceType::class,
            service('sylius.repository.product_attribute'),
            service('sylius.repository.locale'),
            service('sylius.form_registry.attribute_type'),
            service('sylius.form.data_transformer.locale_to_code'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product', ProductType::class)
        ->args([
            '%sylius.model.product.class%',
            '%sylius.model.product_option.class%',
            '%sylius.form.type.product.validation_groups%',
            service('sylius.resolver.product_variant'),
            service('sylius.factory.product_attribute_value'),
            service('sylius.translation_locale_provider'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_translation', ProductTranslationType::class)
        ->args([
            '%sylius.model.product_translation.class%',
            '%sylius.form.type.product_translation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_choice', ProductChoiceType::class)
        ->args([service('sylius.repository.product')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_code_choice', ProductCodeChoiceType::class)
        ->args([service('sylius.repository.product')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_generate_variants', ProductGenerateVariantsType::class)
        ->args([
            '%sylius.model.product.class%',
            '%sylius.form.type.product_generate_variants.validation_groups%',
            service('sylius.form.event_subscriber.generate_product_variants'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_option', ProductOptionType::class)
        ->args([
            '%sylius.model.product_option.class%',
            '%sylius.form.type.product_option.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_option_translation', ProductOptionTranslationType::class)
        ->args([
            '%sylius.model.product_option_translation.class%',
            '%sylius.form.type.product_option_translation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_option_value', ProductOptionValueType::class)
        ->args([
            '%sylius.model.product_option_value.class%',
            '%sylius.form.type.product_option_value.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_option_value_translation', ProductOptionValueTranslationType::class)
        ->args([
            '%sylius.model.product_option_value_translation.class%',
            '%sylius.form.type.product_option_value_translation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_variant', ProductVariantType::class)
        ->args([
            '%sylius.model.product_variant.class%',
            '%sylius.form.type.product_variant.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_variant_translation', ProductVariantTranslationType::class)
        ->args([
            '%sylius.model.product_variant_translation.class%',
            '%sylius.form.type.product_variant_translation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_variant_generation', ProductVariantGenerationType::class)
        ->args([
            '%sylius.model.product_variant.class%',
            '%sylius.form.type.product_variant_generation.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.event_subscriber.generate_product_variants', GenerateProductVariantsSubscriber::class)
        ->args([
            service('sylius.generator.product_variant'),
            service('request_stack'),
        ])
    ;

    $services
        ->set('sylius.form.type.data_transformer.products_to_product_associations', ProductsToProductAssociationsTransformer::class)
        ->args([
            service('sylius.factory.product_association'),
            service('sylius.repository.product'),
            service('sylius.repository.product_association_type'),
        ])
    ;

    $services
        ->set('sylius.form.type.product_option_value_choice', ProductOptionValueChoiceType::class)
        ->args([service('sylius.resolver.available_product_option_values')])
        ->tag('form.type')
    ;
};
