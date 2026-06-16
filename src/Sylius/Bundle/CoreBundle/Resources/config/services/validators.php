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

use Sylius\Bundle\CoreBundle\Validator\Constraints\AllowedImageMimeTypesValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemAvailabilityValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemQuantityRangeValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemVariantEnabledValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelCodeCollectionValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelDefaultLocaleEnabledValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CountryCodeExistsValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\CustomerGroupCodeExistsValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ExistingChannelCodeValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasAllPricesDefinedValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasAllVariantPricesDefinedValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntityValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\LocalesAwareValidAttributeValueValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\MaxIntegerValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\OrderPaymentMethodEligibilityValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\OrderProductEligibilityValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\OrderShippingMethodEligibilityValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductCodeExistsValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductImageVariantsBelongToOwnerValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProductVariantCodeExistsValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\PromotionConfigurationChannelCodesValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ProvinceCodeExistsValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\RegisteredUserValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ResendOrderConfirmationEmailWithValidOrderStateValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ResendShipmentConfirmationEmailWithValidShipmentStateValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\TaxonCodeExistsValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\TranslationForExistingLocalesValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\UniqueReviewerEmailValidator;
use Sylius\Bundle\CoreBundle\Validator\Constraints\ZoneCodeExistsValidator;
use Sylius\Bundle\CoreBundle\Validator\Initializer\CustomerInitializer;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services
        ->set('sylius.validator.has_enabled_entity', HasEnabledEntityValidator::class)
        ->args([
            service('doctrine'),
            service('property_accessor'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_has_enabled_entity'])
    ;

    $services
        ->set('sylius.validator.customer_initializer', CustomerInitializer::class)
        ->args([service('sylius.canonicalizer')])
        ->tag('validator.initializer')
    ;

    $services
        ->set('sylius.validator.registered_user', RegisteredUserValidator::class)
        ->args([service('sylius.repository.customer')])
        ->tag('validator.constraint_validator', ['alias' => 'registered_user_validator'])
    ;

    $services
        ->set('sylius.validator.cart_item_availability', CartItemAvailabilityValidator::class)
        ->args([service('sylius.checker.inventory.availability')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_cart_item_availability'])
    ;

    $services
        ->set('sylius.validator.cart_item_variant_enabled', CartItemVariantEnabledValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_cart_item_variant_enabled'])
    ;

    $services
        ->set('sylius.validator.has_all_prices_defined', HasAllPricesDefinedValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_has_all_prices_defined'])
    ;

    $services
        ->set('sylius.validator.has_all_variant_prices_defined', HasAllVariantPricesDefinedValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_has_all_variant_prices_defined'])
    ;

    $services
        ->set('sylius.validator.translation_for_existing_locales', TranslationForExistingLocalesValidator::class)
        ->args([service('sylius.repository.locale')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_translation_for_existing_locales'])
    ;

    $services
        ->set('sylius.validator.unique_reviewer_email', UniqueReviewerEmailValidator::class)
        ->args([
            service('sylius.repository.shop_user'),
            service('security.token_storage'),
            service('security.authorization_checker'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_unique_reviewer_email_validator'])
    ;

    $services
        ->set('sylius.validator.locales_aware_valid_attribute_value', LocalesAwareValidAttributeValueValidator::class)
        ->args([
            service('sylius.registry.attribute_type'),
            service('sylius.translation_locale_provider'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_valid_attribute_value_validator'])
    ;

    $services
        ->set('sylius.validator.order_shipping_method_eligibility', OrderShippingMethodEligibilityValidator::class)
        ->args([service('sylius.checker.shipping_method_eligibility')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_order_shipping_method_eligibility_validator'])
    ;

    $services
        ->set('sylius.validator.order_payment_method_eligibility', OrderPaymentMethodEligibilityValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_order_payment_method_eligibility_validator'])
    ;

    $services
        ->set('sylius.validator.order_product_eligibility', OrderProductEligibilityValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_order_product_eligibility_validator'])
    ;

    $services
        ->set('sylius.validator.channel_default_locale_enabled', ChannelDefaultLocaleEnabledValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_channel_default_locale_enabled'])
    ;

    $services
        ->set('sylius.validator.promotion_configuration_channel_codes', PromotionConfigurationChannelCodesValidator::class)
        ->args([service('sylius.repository.channel')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_promotion_configuration_channel_codes'])
    ;

    $services
        ->set('sylius.validator.channel_code_among_existing_ones', ExistingChannelCodeValidator::class)
        ->args([service('sylius.repository.channel')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_channel_code_among_existing_ones'])
    ;

    $services
        ->set('sylius.validator.channel_code_collection', ChannelCodeCollectionValidator::class)
        ->args([
            service('sylius.repository.channel'),
            service('property_accessor'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_channel_code_collection'])
    ;

    $services
        ->set('sylius.validator.customer_group_code_exists', CustomerGroupCodeExistsValidator::class)
        ->args([service('sylius.repository.customer_group')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_customer_group_code_exists'])
    ;

    $services
        ->set('sylius.validator.country_code_exists', CountryCodeExistsValidator::class)
        ->args([service('sylius.repository.country')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_country_code_exists'])
    ;

    $services
        ->set('sylius.validator.taxon_code_exists', TaxonCodeExistsValidator::class)
        ->args([service('sylius.repository.taxon')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_taxon_code_exists'])
    ;

    $services
        ->set('sylius.validator.product_code_exists', ProductCodeExistsValidator::class)
        ->args([service('sylius.repository.product')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_product_code_exists'])
    ;

    $services
        ->set('sylius.validator.product_variant_code_exists', ProductVariantCodeExistsValidator::class)
        ->args([service('sylius.repository.product_variant')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_product_variant_code_exists'])
    ;

    $services
        ->set('sylius.validator.resend_order_confirmation_email_with_valid_order_state', ResendOrderConfirmationEmailWithValidOrderStateValidator::class)
        ->args([
            service('sylius.repository.order'),
            '%sylius_order.resend_order_confirmation_email.order_states%',
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_order_confirmation_with_valid_order_state'])
    ;

    $services
        ->set('sylius.validator.product_image_variants_belong_to_owner', ProductImageVariantsBelongToOwnerValidator::class)
        ->tag('validator.constraint_validator', ['alias' => 'sylius_product_image_variants_belong_to_owner'])
    ;

    $services
        ->set('sylius.validator.resend_shipment_confirmation_email_with_valid_order_state', ResendShipmentConfirmationEmailWithValidShipmentStateValidator::class)
        ->args([service('sylius.repository.shipment')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_shipment_confirmation_with_valid_shipment_state'])
    ;

    $services
        ->set('sylius.validator.max_integer', MaxIntegerValidator::class)
        ->args(['%sylius_core.max_int_value%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_max_integer'])
    ;

    $services
        ->set('sylius.validator.province_code_exists', ProvinceCodeExistsValidator::class)
        ->args([service('sylius.repository.province')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_province_code_exists'])
    ;

    $services
        ->set('sylius.validator.zone_code_exists', ZoneCodeExistsValidator::class)
        ->args([service('sylius.repository.zone')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_zone_code_exists'])
    ;

    $services
        ->set('sylius.validator.cart_item_quantity_range', CartItemQuantityRangeValidator::class)
        ->args([
            service('property_accessor'),
            '%sylius.order_item_quantity_modifier.limit%',
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_cart_item_quantity_range'])
    ;

    $services
        ->set('sylius.validator.allowed_image_mime_types', AllowedImageMimeTypesValidator::class)
        ->args(['%sylius_core.allowed_images_mime_types%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_image_allowed_mime_types_validator'])
    ;
};
