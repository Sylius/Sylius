<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();

    $services->set('sylius.validator.has_enabled_entity', 'Sylius\Bundle\CoreBundle\Validator\Constraints\HasEnabledEntityValidator')
        ->args([
            service('doctrine'),
            service('property_accessor'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_has_enabled_entity']);

    $services->set('sylius.validator.customer_initializer', 'Sylius\Bundle\CoreBundle\Validator\Initializer\CustomerInitializer')
        ->args([service('sylius.canonicalizer')])
        ->tag('validator.initializer');

    $services->set('sylius.validator.registered_user', 'Sylius\Bundle\CoreBundle\Validator\Constraints\RegisteredUserValidator')
        ->args([service('sylius.repository.customer')])
        ->tag('validator.constraint_validator', ['alias' => 'registered_user_validator']);

    $services->set('sylius.validator.cart_item_availability', 'Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemAvailabilityValidator')
        ->args([service('sylius.checker.inventory.availability')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_cart_item_availability']);

    $services->set('sylius.validator.cart_item_variant_enabled', 'Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemVariantEnabledValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_cart_item_variant_enabled']);

    $services->set('sylius.validator.has_all_prices_defined', 'Sylius\Bundle\CoreBundle\Validator\Constraints\HasAllPricesDefinedValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_has_all_prices_defined']);

    $services->set('sylius.validator.has_all_variant_prices_defined', 'Sylius\Bundle\CoreBundle\Validator\Constraints\HasAllVariantPricesDefinedValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_has_all_variant_prices_defined']);

    $services->set('sylius.validator.translation_for_existing_locales', 'Sylius\Bundle\CoreBundle\Validator\Constraints\TranslationForExistingLocalesValidator')
        ->args([service('sylius.repository.locale')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_translation_for_existing_locales']);

    $services->set('sylius.validator.unique_reviewer_email', 'Sylius\Bundle\CoreBundle\Validator\Constraints\UniqueReviewerEmailValidator')
        ->args([
            service('sylius.repository.shop_user'),
            service('security.token_storage'),
            service('security.authorization_checker'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_unique_reviewer_email_validator']);

    $services->set('sylius.validator.locales_aware_valid_attribute_value', 'Sylius\Bundle\CoreBundle\Validator\Constraints\LocalesAwareValidAttributeValueValidator')
        ->args([
            service('sylius.registry.attribute_type'),
            service('sylius.translation_locale_provider'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_valid_attribute_value_validator']);

    $services->set('sylius.validator.order_shipping_method_eligibility', 'Sylius\Bundle\CoreBundle\Validator\Constraints\OrderShippingMethodEligibilityValidator')
        ->args([service('sylius.checker.shipping_method_eligibility')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_order_shipping_method_eligibility_validator']);

    $services->set('sylius.validator.order_payment_method_eligibility', 'Sylius\Bundle\CoreBundle\Validator\Constraints\OrderPaymentMethodEligibilityValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_order_payment_method_eligibility_validator']);

    $services->set('sylius.validator.order_product_eligibility', 'Sylius\Bundle\CoreBundle\Validator\Constraints\OrderProductEligibilityValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_order_product_eligibility_validator']);

    $services->set('sylius.validator.channel_default_locale_enabled', 'Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelDefaultLocaleEnabledValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_channel_default_locale_enabled']);

    $services->set('sylius.validator.channel_code_among_existing_ones', 'Sylius\Bundle\CoreBundle\Validator\Constraints\ExistingChannelCodeValidator')
        ->args([service('sylius.repository.channel')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_channel_code_among_existing_ones']);

    $services->set('sylius.validator.channel_code_collection', 'Sylius\Bundle\CoreBundle\Validator\Constraints\ChannelCodeCollectionValidator')
        ->args([
            service('sylius.repository.channel'),
            service('property_accessor'),
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_channel_code_collection']);

    $services->set('sylius.validator.customer_group_code_exists', 'Sylius\Bundle\CoreBundle\Validator\Constraints\CustomerGroupCodeExistsValidator')
        ->args([service('sylius.repository.customer_group')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_customer_group_code_exists']);

    $services->set('sylius.validator.country_code_exists', 'Sylius\Bundle\CoreBundle\Validator\Constraints\CountryCodeExistsValidator')
        ->args([service('sylius.repository.country')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_country_code_exists']);

    $services->set('sylius.validator.taxon_code_exists', 'Sylius\Bundle\CoreBundle\Validator\Constraints\TaxonCodeExistsValidator')
        ->args([service('sylius.repository.taxon')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_taxon_code_exists']);

    $services->set('sylius.validator.product_code_exists', 'Sylius\Bundle\CoreBundle\Validator\Constraints\ProductCodeExistsValidator')
        ->args([service('sylius.repository.product')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_product_code_exists']);

    $services->set('sylius.validator.product_variant_code_exists', 'Sylius\Bundle\CoreBundle\Validator\Constraints\ProductVariantCodeExistsValidator')
        ->args([service('sylius.repository.product_variant')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_product_variant_code_exists']);

    $services->set('sylius.validator.resend_order_confirmation_email_with_valid_order_state', 'Sylius\Bundle\CoreBundle\Validator\Constraints\ResendOrderConfirmationEmailWithValidOrderStateValidator')
        ->args([
            service('sylius.repository.order'),
            '%sylius_order.resend_order_confirmation_email.order_states%',
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_order_confirmation_with_valid_order_state']);

    $services->set('sylius.validator.product_image_variants_belong_to_owner', 'Sylius\Bundle\CoreBundle\Validator\Constraints\ProductImageVariantsBelongToOwnerValidator')
        ->tag('validator.constraint_validator', ['alias' => 'sylius_product_image_variants_belong_to_owner']);

    $services->set('sylius.validator.resend_shipment_confirmation_email_with_valid_order_state', 'Sylius\Bundle\CoreBundle\Validator\Constraints\ResendShipmentConfirmationEmailWithValidShipmentStateValidator')
        ->args([service('sylius.repository.shipment')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_shipment_confirmation_with_valid_shipment_state']);

    $services->set('sylius.validator.max_integer', 'Sylius\Bundle\CoreBundle\Validator\Constraints\MaxIntegerValidator')
        ->args(['%sylius_core.max_int_value%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_max_integer']);

    $services->set('sylius.validator.province_code_exists', 'Sylius\Bundle\CoreBundle\Validator\Constraints\ProvinceCodeExistsValidator')
        ->args([service('sylius.repository.province')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_province_code_exists']);

    $services->set('sylius.validator.zone_code_exists', 'Sylius\Bundle\CoreBundle\Validator\Constraints\ZoneCodeExistsValidator')
        ->args([service('sylius.repository.zone')])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_zone_code_exists']);

    $services->set('sylius.validator.cart_item_quantity_range', 'Sylius\Bundle\CoreBundle\Validator\Constraints\CartItemQuantityRangeValidator')
        ->args([
            service('property_accessor'),
            '%sylius.order_item_quantity_modifier.limit%',
        ])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_cart_item_quantity_range']);

    $services->set('sylius.validator.allowed_image_mime_types', 'Sylius\Bundle\CoreBundle\Validator\Constraints\AllowedImageMimeTypesValidator')
        ->args(['%sylius_core.allowed_images_mime_types%'])
        ->tag('validator.constraint_validator', ['alias' => 'sylius_image_allowed_mime_types_validator']);
};
