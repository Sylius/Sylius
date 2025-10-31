<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();

    $services->set('sylius_api.denormalizer.address', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\AddressDenormalizer')
        ->args([
            service('serializer.normalizer.object'),
            '%sylius.model.address.class%',
            '%sylius.model.address.interface%',
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.denormalizer.command_arguments', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandArgumentsDenormalizer')
        ->args([
            service('sylius_api.denormalizer.command'),
            service('sylius_api.converter.iri_to_identifier'),
        ])
        ->tag('serializer.normalizer', ['priority' => 128]);

    $services->set('sylius_api.denormalizer.command', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandDenormalizer')
        ->args([
            service('api_platform.serializer.normalizer.item'),
            service('serializer.name_converter.metadata_aware'),
        ])
        ->tag('serializer.normalizer');

    $services->set('sylius_api.normalizer.product', 'Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductNormalizer')
        ->args([
            service('sylius.resolver.product_variant'),
            service('api_platform.symfony.iri_converter'),
            service('sylius.section_resolver.uri_based'),
            ['' => 'sylius:shop:product:index', '' => 'sylius:shop:product:show'],
            service('serializer.normalizer.object'),
            ['' => 'sylius:shop:product:index:default_variant', '' => 'sylius:shop:product:show:default_variant'],
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.denormalizer.product_attribute_value', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductAttributeValueDenormalizer')
        ->args([service('api_platform.symfony.iri_converter')])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.denormalizer.product', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductDenormalizer')
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.normalizer.product_attribute_value', 'Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductAttributeValueNormalizer')
        ->args([
            service('sylius.provider.locale.channel_based'),
            '%locale%',
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.normalizer.product_option_value', 'Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductOptionValueNormalizer')
        ->args([service('sylius.translatable_entity_locale_assigner')])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.normalizer.image', 'Sylius\Bundle\ApiBundle\Serializer\Normalizer\ImageNormalizer')
        ->args([
            service('liip_imagine.cache.manager'),
            service('request_stack'),
            '%sylius_api.default_image_filter%',
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.normalizer.command', 'Sylius\Bundle\ApiBundle\Serializer\Normalizer\CommandNormalizer')
        ->args([service('serializer.normalizer.object')])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.normalizer.product_variant', 'Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductVariantNormalizer')
        ->args([
            service('sylius.calculator.product_variant_price'),
            service('sylius.checker.inventory.availability'),
            service('sylius.section_resolver.uri_based'),
            service('api_platform.symfony.iri_converter'),
            ['' => 'sylius:shop:product_variant:index', '' => 'sylius:shop:product_variant:show', '' => 'sylius:shop:product:index', '' => 'sylius:shop:product:show'],
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.normalizer.shipping_method', 'Sylius\Bundle\ApiBundle\Serializer\Normalizer\ShippingMethodNormalizer')
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius.repository.order'),
            service('sylius.repository.shipment'),
            service('sylius.registry.shipping_calculator'),
            service('Symfony\Component\HttpFoundation\RequestStack'),
            ['' => 'sylius:shop:shipping_method:index'],
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.normalizer.generated_promotion_coupons', 'Sylius\Bundle\ApiBundle\Serializer\Normalizer\GeneratedPromotionCouponsNormalizer')
        ->args([
            service('sylius.section_resolver.uri_based'),
            ['' => 'sylius:admin:promotion_coupon:index'],
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.denormalizer.zone', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ZoneDenormalizer')
        ->args([
            service('serializer.normalizer.object'),
            service('sylius.section_resolver.uri_based'),
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.denormalizer.translatable', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\TranslatableDenormalizer')
        ->args([service('sylius.translation_locale_provider')])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.normalizer.date_time', 'Symfony\Component\Serializer\Normalizer\DateTimeNormalizer')
        ->args([['datetime_format' => 'Y-m-d H:i:s']])
        ->tag('serializer.normalizer');

    $services->set('sylius_api.denormalizer.channel_price_history_config', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ChannelPriceHistoryConfigDenormalizer')
        ->args([
            service('api_platform.symfony.iri_converter'),
            service('sylius.factory.channel_price_history_config'),
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.denormalizer.channel', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ChannelDenormalizer')
        ->args([
            service('sylius.factory.channel_price_history_config'),
            service('sylius.factory.shop_billing_data'),
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.denormalizer.numeric_to_string.tax_rate', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\NumericToStringDenormalizer')
        ->args([
            '%sylius.model.tax_rate.class%',
            'amount',
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.denormalizer.numeric_to_string.exchange_rate', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\NumericToStringDenormalizer')
        ->args([
            '%sylius.model.exchange_rate.class%',
            'ratio',
        ])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.denormalizer.customer', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CustomerDenormalizer')
        ->args([service('clock')])
        ->tag('serializer.normalizer', ['priority' => 64]);

    $services->set('sylius_api.denormalizer.translatable_locale_key', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\TranslatableLocaleKeyDenormalizer')
        ->tag('serializer.normalizer', ['priority' => 96]);

    $services->set('sylius_api.denormalizer.product_variant_channel_pricings_channel_code_key', 'Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductVariantChannelPricingsChannelCodeKeyDenormalizer')
        ->tag('serializer.normalizer', ['priority' => 96]);

    $services->set('sylius_api.normalizer.doctrine_collection_values', 'Sylius\Bundle\ApiBundle\Serializer\Normalizer\DoctrineCollectionValuesNormalizer')
        ->tag('serializer.normalizer', ['priority' => 64]);
};
