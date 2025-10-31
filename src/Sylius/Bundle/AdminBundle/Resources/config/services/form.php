<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.form.type.product_review.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.admin.password_reset_request.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.admin.reset_password.validation_groups', ['sylius']);

    $services->set('sylius_admin.form.type.request_password_reset', 'Sylius\Bundle\AdminBundle\Form\RequestPasswordResetType')
        ->args(['%sylius.form.type.admin.password_reset_request.validation_groups%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.reset_password', 'Sylius\Bundle\AdminBundle\Form\Type\ResetPasswordType')
        ->args(['%sylius.form.type.admin.reset_password.validation_groups%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.grid_filter.ux_autocomplete', 'Sylius\Bundle\AdminBundle\Form\Type\Grid\Filter\UxAutocompleteFilterType')
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.type.grid_filter.ux_translatable_autocomplete', 'Sylius\Bundle\AdminBundle\Form\Type\Grid\Filter\UxTranslatableAutocompleteFilterType')
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.extension.type.promotion', 'Sylius\Bundle\AdminBundle\Form\Extension\PromotionTypeExtension')
        ->args([
            '%sylius.promotion_rules%',
            '%sylius.promotion_actions%',
        ])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion.promotion_action', 'Sylius\Bundle\AdminBundle\Form\Extension\Promotion\PromotionActionTypeExtension')
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion_action.product_filter_configuration', 'Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Action\ProductFilterConfigurationTypeExtension')
        ->args([service('sylius.form.type.data_transformer.products_to_codes')])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion_action.taxon_filter_configuration', 'Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Action\TaxonFilterConfigurationTypeExtension')
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion.promotion_rule', 'Sylius\Bundle\AdminBundle\Form\Extension\Promotion\PromotionRuleTypeExtension')
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion_rule.contains_product_configuration', 'Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Rule\ContainsProductConfigurationTypeExtension')
        ->args([service('sylius.repository.product')])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion_rule.has_taxon_configuration', 'Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Rule\HasTaxonConfigurationTypeExtension')
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion_rule.total_of_items_from_taxon_configuration', 'Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Rule\TotalOfItemsFromTaxonConfigurationTypeExtension')
        ->args([service('sylius.repository.taxon')])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.type.admin_user', 'Sylius\Bundle\AdminBundle\Form\Type\AdminUserType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.address', 'Sylius\Bundle\AdminBundle\Form\Type\AddressType')
        ->args([service('sylius.repository.country')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion', 'Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionType')
        ->args([
            '%sylius.catalog_promotion.scopes_types%',
            '%sylius.catalog_promotion.actions_types%',
        ])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion_action', 'Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionActionType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion_scope', 'Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScopeType')
        ->args([tagged_iterator('sylius_admin.catalog_promotion.scope_configuration_type', indexAttribute: 'key')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion_scope.for_products_scope_configuration', 'Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScope\ForProductsScopeConfigurationType')
        ->args([service('sylius.form.type.data_transformer.products_to_codes')])
        ->tag('sylius_admin.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_products%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion_scope.for_taxons_scope_configuration', 'Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScope\ForTaxonsScopeConfigurationType')
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('sylius_admin.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_taxons%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion_scope.for_variants_scope_configuration', 'Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScope\ForVariantsScopeConfigurationType')
        ->args([service('sylius.form.type.data_transformer.product_variants_to_codes')])
        ->tag('sylius_admin.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_variants%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.channel', 'Sylius\Bundle\AdminBundle\Form\Type\ChannelType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.channel_price_history_config', 'Sylius\Bundle\AdminBundle\Form\Type\ChannelPriceHistoryConfigType')
        ->args([
            service('sylius.repository.taxon'),
            inline_service('\Symfony\Component\Form\Extension\Core\DataMapper\DataMapper'),
        ])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.country', 'Sylius\Bundle\AdminBundle\Form\Type\CountryType')
        ->args([service('sylius.repository.country')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.currency', 'Sylius\Bundle\AdminBundle\Form\Type\CurrencyType')
        ->args([service('sylius.repository.currency')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.customer', 'Sylius\Bundle\AdminBundle\Form\Type\CustomerType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.customer_group', 'Sylius\Bundle\AdminBundle\Form\Type\CustomerGroupType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.exchange_rate', 'Sylius\Bundle\AdminBundle\Form\Type\ExchangeRateType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.locale', 'Sylius\Bundle\AdminBundle\Form\Type\LocaleType')
        ->args([service('sylius.repository.locale')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.order', 'Sylius\Bundle\AdminBundle\Form\Type\OrderType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.payment_method', 'Sylius\Bundle\AdminBundle\Form\Type\PaymentMethodType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product', 'Sylius\Bundle\AdminBundle\Form\Type\ProductType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_association_type', 'Sylius\Bundle\AdminBundle\Form\Type\ProductAssociationTypeType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_attribute', 'Sylius\Bundle\AdminBundle\Form\Type\ProductAttributeType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_attribute_autocomplete', 'Sylius\Bundle\AdminBundle\Form\Type\ProductAttributeAutocompleteType')
        ->args(['%sylius.model.product_attribute.class%'])
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.type.product_autocomplete', 'Sylius\Bundle\AdminBundle\Form\Type\ProductAutocompleteType')
        ->args(['%sylius.model.product.class%'])
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.type.product_generate_variants', 'Sylius\Bundle\AdminBundle\Form\Type\ProductGenerateVariantsType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_image', 'Sylius\Bundle\AdminBundle\Form\Type\ProductImageType')
        ->args(['%sylius.model.product_variant.class%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_option', 'Sylius\Bundle\AdminBundle\Form\Type\ProductOptionType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_review', 'Sylius\Bundle\AdminBundle\Form\Type\ProductReviewType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_variant', 'Sylius\Bundle\AdminBundle\Form\Type\ProductVariantType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_variant_autocomplete', 'Sylius\Bundle\AdminBundle\Form\Type\ProductVariantAutocompleteType')
        ->args(['%sylius.model.product_variant.class%'])
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.type.promotion', 'Sylius\Bundle\AdminBundle\Form\Type\PromotionType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.promotion_coupon', 'Sylius\Bundle\AdminBundle\Form\Type\PromotionCouponType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.promotion_coupon_generator_instruction', 'Sylius\Bundle\AdminBundle\Form\Type\PromotionCouponGeneratorInstructionType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.shipping_category', 'Sylius\Bundle\AdminBundle\Form\Type\ShippingCategoryType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.shipping_method', 'Sylius\Bundle\AdminBundle\Form\Type\ShippingMethodType')
        ->args(['%sylius.shipping_method_rules%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.shipping_method_rule', 'Sylius\Bundle\AdminBundle\Form\Type\ShippingMethodRuleType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.shipment_ship', 'Sylius\Bundle\AdminBundle\Form\Type\ShipmentShipType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.shop_user', 'Sylius\Bundle\AdminBundle\Form\Type\ShopUserType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.tax_category', 'Sylius\Bundle\AdminBundle\Form\Type\TaxCategoryType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.taxon', 'Sylius\Bundle\AdminBundle\Form\Type\TaxonType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.taxon_autocomplete', 'Sylius\Bundle\AdminBundle\Form\Type\TaxonAutocompleteType')
        ->args(['%sylius.model.taxon.class%'])
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.type.tax_rate', 'Sylius\Bundle\AdminBundle\Form\Type\TaxRateType')
        ->tag('form.type');

    $services->set('sylius_admin.form.type.translatable_autocomplete', 'Sylius\Bundle\AdminBundle\Form\Type\TranslatableAutocompleteType')
        ->args([service('sylius.context.locale')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.zone', 'Sylius\Bundle\AdminBundle\Form\Type\ZoneType')
        ->tag('form.type');
};
