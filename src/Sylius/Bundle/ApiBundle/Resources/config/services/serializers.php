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

use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\AddressDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ChannelDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ChannelPriceHistoryConfigDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandArgumentsDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CommandDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\CustomerDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\NumericToStringDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductAttributeValueDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ProductVariantChannelPricingsChannelCodeKeyDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\TranslatableDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\TranslatableLocaleKeyDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Denormalizer\ZoneDenormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\CommandNormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\DoctrineCollectionValuesNormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\GeneratedPromotionCouponsNormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ImageNormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductAttributeValueNormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductNormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductOptionValueNormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ProductVariantNormalizer;
use Sylius\Bundle\ApiBundle\Serializer\Normalizer\ShippingMethodNormalizer;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius_api.denormalizer.address', AddressDenormalizer::class)
        ->args([
            service('serializer.normalizer.object'),
            '%sylius.model.address.class%',
            '%sylius.model.address.interface%',
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.denormalizer.command_arguments', CommandArgumentsDenormalizer::class)
        ->args([
            service('sylius_api.denormalizer.command'),
            service('sylius_api.converter.iri_to_identifier'),
        ])
        ->tag('serializer.normalizer', ['priority' => 128])
    ;

    $services
        ->set('sylius_api.denormalizer.command', CommandDenormalizer::class)
        ->args([
            service('api_platform.serializer.normalizer.item'),
            service('serializer.name_converter.metadata_aware'),
        ])
        ->tag('serializer.normalizer')
    ;

    $services
        ->set('sylius_api.normalizer.product', ProductNormalizer::class)
        ->args([
            service('sylius.resolver.product_variant'),
            service('api_platform.symfony.iri_converter'),
            service('sylius.section_resolver.uri_based'),
            ['sylius:shop:product:index', 'sylius:shop:product:show'],
            service('serializer.normalizer.object'),
            ['sylius:shop:product:index:default_variant', 'sylius:shop:product:show:default_variant'],
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.denormalizer.product_attribute_value', ProductAttributeValueDenormalizer::class)
        ->args([service('api_platform.symfony.iri_converter')])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.denormalizer.product', ProductDenormalizer::class)
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.normalizer.product_attribute_value', ProductAttributeValueNormalizer::class)
        ->args([
            service('sylius.provider.locale.channel_based'),
            '%locale%',
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.normalizer.product_option_value', ProductOptionValueNormalizer::class)
        ->args([service('sylius.translatable_entity_locale_assigner')])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.normalizer.image', ImageNormalizer::class)
        ->args([
            service('liip_imagine.cache.manager'),
            service('request_stack'),
            '%sylius_api.default_image_filter%',
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.normalizer.command', CommandNormalizer::class)
        ->args([service('serializer.normalizer.object')])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.normalizer.product_variant', ProductVariantNormalizer::class)
        ->args([
            service('sylius.calculator.product_variant_price'),
            service('sylius.checker.inventory.availability'),
            service('sylius.section_resolver.uri_based'),
            service('api_platform.symfony.iri_converter'),
            ['sylius:shop:product_variant:index', 'sylius:shop:product_variant:show', 'sylius:shop:product:index', 'sylius:shop:product:show'],
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.normalizer.shipping_method', ShippingMethodNormalizer::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            service('sylius.repository.order'),
            service('sylius.repository.shipment'),
            service('sylius.registry.shipping_calculator'),
            service(RequestStack::class),
            ['sylius:shop:shipping_method:index'],
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.normalizer.generated_promotion_coupons', GeneratedPromotionCouponsNormalizer::class)
        ->args([
            service('sylius.section_resolver.uri_based'),
            ['sylius:admin:promotion_coupon:index'],
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.denormalizer.zone', ZoneDenormalizer::class)
        ->args([
            service('serializer.normalizer.object'),
            service('sylius.section_resolver.uri_based'),
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.denormalizer.translatable', TranslatableDenormalizer::class)
        ->args([service('sylius.translation_locale_provider')])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.normalizer.date_time', DateTimeNormalizer::class)
        ->args([['datetime_format' => 'Y-m-d H:i:s']])
        ->tag('serializer.normalizer')
    ;

    $services
        ->set('sylius_api.denormalizer.channel_price_history_config', ChannelPriceHistoryConfigDenormalizer::class)
        ->args([
            service('api_platform.symfony.iri_converter'),
            service('sylius.factory.channel_price_history_config'),
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.denormalizer.channel', ChannelDenormalizer::class)
        ->args([
            service('sylius.factory.channel_price_history_config'),
            service('sylius.factory.shop_billing_data'),
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.denormalizer.numeric_to_string.tax_rate', NumericToStringDenormalizer::class)
        ->args([
            '%sylius.model.tax_rate.class%',
            'amount',
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.denormalizer.numeric_to_string.exchange_rate', NumericToStringDenormalizer::class)
        ->args([
            '%sylius.model.exchange_rate.class%',
            'ratio',
        ])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.denormalizer.customer', CustomerDenormalizer::class)
        ->args([service('clock')])
        ->tag('serializer.normalizer', ['priority' => 64])
    ;

    $services
        ->set('sylius_api.denormalizer.translatable_locale_key', TranslatableLocaleKeyDenormalizer::class)
        ->tag('serializer.normalizer', ['priority' => 96])
    ;

    $services
        ->set('sylius_api.denormalizer.product_variant_channel_pricings_channel_code_key', ProductVariantChannelPricingsChannelCodeKeyDenormalizer::class)
        ->tag('serializer.normalizer', ['priority' => 96])
    ;

    $services
        ->set('sylius_api.normalizer.doctrine_collection_values', DoctrineCollectionValuesNormalizer::class)
        ->tag('serializer.normalizer', ['priority' => 64])
    ;
};
