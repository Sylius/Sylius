<?php

namespace Symfony\Component\DependencyInjection\Loader\Configurator;

return static function(ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.form.type.product_review.validation_groups', ['sylius', 'sylius_review']);
    $parameters->set('sylius.form.type.admin_user.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.shop_user.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.shop_user_registration.validation_groups', ['sylius', 'sylius_user_registration']);
    $parameters->set('sylius.form.type.customer_guest.validation_groups', ['sylius_customer_guest']);
    $parameters->set('sylius.form.type.customer_checkout_guest.validation_groups', ['sylius_customer_checkout_guest']);
    $parameters->set('sylius.form.type.customer_simple_registration.validation_groups', ['sylius', 'sylius_user_registration']);
    $parameters->set('sylius.form.type.customer_registration.validation_groups', ['sylius', 'sylius_user_registration', 'sylius_customer_profile']);
    $parameters->set('sylius.form.type.add_to_cart.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.channel_pricing.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.product_image.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.channel_price_history_config.validation_groups', ['sylius']);
    $parameters->set('sylius.catalog_promotion.action.fixed_discount', \Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator::TYPE);
    $parameters->set('sylius.catalog_promotion.action.percentage_discount', \Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator::TYPE);
    $parameters->set('sylius.catalog_promotion.scope.for_products', \Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForProductScopeVariantChecker::TYPE);
    $parameters->set('sylius.catalog_promotion.scope.for_taxons', \Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker::TYPE);
    $parameters->set('sylius.catalog_promotion.scope.for_variants', \Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker::TYPE);

    $services->set('sylius.form.extension.type.address', 'Sylius\Bundle\CoreBundle\Form\Extension\AddressTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.channel', 'Sylius\Bundle\CoreBundle\Form\Extension\ChannelTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.order', 'Sylius\Bundle\CoreBundle\Form\Extension\OrderTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.cart', 'Sylius\Bundle\CoreBundle\Form\Extension\CartTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.cart_item', 'Sylius\Bundle\CoreBundle\Form\Extension\CartItemTypeExtension')
        ->args(['%sylius.order_item_quantity_modifier.limit%'])
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.catalog_promotion', 'Sylius\Bundle\CoreBundle\Form\Extension\CatalogPromotionTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.payment_method', 'Sylius\Bundle\CoreBundle\Form\Extension\PaymentMethodTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.tax_rate', 'Sylius\Bundle\CoreBundle\Form\Extension\TaxRateTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.taxon', 'Sylius\Bundle\CoreBundle\Form\Extension\TaxonTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.promotion', 'Sylius\Bundle\CoreBundle\Form\Extension\PromotionTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.promotion_coupon', 'Sylius\Bundle\CoreBundle\Form\Extension\PromotionCouponTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.promotion_filter_collection', 'Sylius\Bundle\CoreBundle\Form\Extension\PromotionFilterCollectionTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.shipping_method', 'Sylius\Bundle\CoreBundle\Form\Extension\ShippingMethodTypeExtension')
        ->args([service('sylius.validator.groups_generator.shipping_method_configuration')])
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.product', 'Sylius\Bundle\CoreBundle\Form\Extension\ProductTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.product_translation', 'Sylius\Bundle\CoreBundle\Form\Extension\ProductTranslationTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.product_variant', 'Sylius\Bundle\CoreBundle\Form\Extension\ProductVariantTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.extension.type.product_variant_generation', 'Sylius\Bundle\CoreBundle\Form\Extension\ProductVariantGenerationTypeExtension')
        ->tag('form.type_extension', ['priority' => 100]);

    $services->set('sylius.form.type.product_review', 'Sylius\Bundle\CoreBundle\Form\Type\Product\ProductReviewType')
        ->args([
            '%sylius.model.product_review.class%',
            '%sylius.form.type.product_review.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.admin_user', 'Sylius\Bundle\CoreBundle\Form\Type\User\AdminUserType')
        ->args([
            '%sylius.model.admin_user.class%',
            '%sylius.form.type.admin_user.validation_groups%',
            '%sylius_locale.locale%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.shop_user', 'Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserType')
        ->args([
            '%sylius.model.shop_user.class%',
            '%sylius.form.type.shop_user.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.shop_user_registration', 'Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserRegistrationType')
        ->args([
            '%sylius.model.shop_user.class%',
            '%sylius.form.type.shop_user_registration.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.product_image', 'Sylius\Bundle\CoreBundle\Form\Type\Product\ProductImageType')
        ->args([
            '%sylius.model.product_image.class%',
            '%sylius.model.product_variant.class%',
            '%sylius.form.type.product_image.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.taxon_image', 'Sylius\Bundle\CoreBundle\Form\Type\Taxon\TaxonImageType')
        ->args(['%sylius.model.taxon_image.class%'])
        ->tag('form.type');

    $services->set('sylius.form.type.avatar_image', 'Sylius\Bundle\CoreBundle\Form\Type\User\AvatarImageType')
        ->args(['%sylius.model.avatar_image.class%'])
        ->tag('form.type');

    $services->set('sylius.form.type.catalog_promotion_action.percentage_discount_action_configuration', 'Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType')
        ->tag('sylius.catalog_promotion.action_configuration_type', ['key' => '%sylius.catalog_promotion.action.percentage_discount%'])
        ->tag('form.type');

    $services->set('sylius.form.type.catalog_promotion_action.channel_based_fixed_discount_action_configuration', 'Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionAction\ChannelBasedFixedDiscountActionConfigurationType')
        ->tag('sylius.catalog_promotion.action_configuration_type', ['key' => '%sylius.catalog_promotion.action.fixed_discount%'])
        ->tag('form.type');

    $services->set('sylius.form.type.catalog_promotion_scope.for_products_scope_configuration', 'Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForProductsScopeConfigurationType')
        ->args([service('sylius.form.type.data_transformer.products_to_codes')])
        ->tag('sylius.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_products%'])
        ->tag('form.type');

    $services->set('sylius.form.type.catalog_promotion_scope.for_taxons_scope_configuration', 'Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForTaxonsScopeConfigurationType')
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('sylius.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_taxons%'])
        ->tag('form.type');

    $services->set('sylius.form.type.catalog_promotion_scope.for_variants_scope_configuration', 'Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForVariantsScopeConfigurationType')
        ->args([service('sylius.form.type.data_transformer.product_variants_to_codes')])
        ->tag('sylius.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_variants%'])
        ->tag('form.type');

    $services->set('sylius.form.type.tax_calculation_strategy_choice', 'Sylius\Bundle\CoreBundle\Form\Type\TaxCalculationStrategyChoiceType')
        ->args(['%sylius.tax_calculation_strategies%'])
        ->tag('form.type');

    $services->set('sylius.form.type.promotion_rule.customer_group_configuration', 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\CustomerGroupConfigurationType')
        ->tag('form.type');

    $services->set('sylius.form.type.promotion_rule.has_taxon_configuration', 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\HasTaxonConfigurationType')
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('form.type');

    $services->set('sylius.form.type.promotion_rule.total_of_items_from_taxon_configuration', 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\TotalOfItemsFromTaxonConfigurationType')
        ->args([service('sylius.repository.taxon')])
        ->tag('form.type');

    $services->set('sylius.form.type.promotion_rule.contains_product_configuration', 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ContainsProductConfigurationType')
        ->args([service('sylius.repository.product')])
        ->tag('form.type');

    $services->set('sylius.form.type.promotion_action.filter.taxon', 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\TaxonFilterConfigurationType')
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('form.type');

    $services->set('sylius.form.type.promotion_action.filter.product', 'Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\ProductFilterConfigurationType')
        ->args([service('sylius.form.type.data_transformer.products_to_codes')])
        ->tag('form.type');

    $services->set('sylius.form.type.data_transformer.taxons_to_codes', 'Sylius\Bundle\CoreBundle\Form\DataTransformer\TaxonsToCodesTransformer')
        ->args([service('sylius.repository.taxon')]);

    $services->set('sylius.form.type.data_transformer.product_variants_to_codes', 'Sylius\Bundle\CoreBundle\Form\DataTransformer\ProductVariantsToCodesTransformer')
        ->args([service('sylius.repository.product_variant')]);

    $services->set('sylius.form.type.data_transformer.products_to_codes', 'Sylius\Bundle\CoreBundle\Form\DataTransformer\ProductsToCodesTransformer')
        ->args([service('sylius.repository.product')]);

    $services->set('sylius.form.type.customer.guest', 'Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerGuestType')
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer_guest.validation_groups%',
            service('sylius.repository.customer'),
            service('sylius.factory.customer'),
            service('sylius.canonicalizer'),
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.customer.checkout_guest', 'Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerCheckoutGuestType')
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer_checkout_guest.validation_groups%',
            service('sylius.repository.customer'),
            service('sylius.factory.customer'),
            service('sylius.canonicalizer'),
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.customer.simple_registration', 'Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerSimpleRegistrationType')
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer_simple_registration.validation_groups%',
            service('sylius.repository.customer'),
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.customer.registration', 'Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerRegistrationType')
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer_registration.validation_groups%',
            service('sylius.repository.customer'),
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.address_choice', 'Sylius\Bundle\CoreBundle\Form\Type\AddressChoiceType')
        ->args([service('sylius.repository.address')])
        ->tag('form.type');

    $services->set('sylius.form.type.order.add_to_cart', 'Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType')
        ->args([
            'Sylius\Bundle\OrderBundle\Controller\AddToCartCommand',
            '%sylius.form.type.add_to_cart.validation_groups%',
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.product.channel_pricing', 'Sylius\Bundle\CoreBundle\Form\Type\Product\ChannelPricingType')
        ->args([
            '%sylius.model.channel_pricing.class%',
            '%sylius.form.type.channel_pricing.validation_groups%',
            service('sylius.repository.channel_pricing'),
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.channels_collection', 'Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType')
        ->args([service('sylius.repository.channel')])
        ->tag('form.type');

    $services->set('sylius.form.type.shipping.calculator.channel_based_flat_rate_configuration', 'Sylius\Bundle\CoreBundle\Form\Type\Shipping\Calculator\ChannelBasedFlatRateConfigurationType')
        ->tag('form.type');

    $services->set('sylius.form.type.shipping.calculator.channel_based_per_unit_rate_configuration', 'Sylius\Bundle\CoreBundle\Form\Type\Shipping\Calculator\ChannelBasedPerUnitRateConfigurationType')
        ->tag('form.type');

    $services->set('sylius.form.type.shop_billing_data', 'Sylius\Bundle\CoreBundle\Form\Type\ShopBillingDataType')
        ->args(['%sylius.model.shop_billing_data.class%'])
        ->tag('form.type');

    $services->set('sylius.form.type.product_taxon_autocomplete_choice', 'Sylius\Bundle\CoreBundle\Form\Type\Taxon\ProductTaxonAutocompleteChoiceType')
        ->args([
            service('sylius.factory.product_taxon'),
            service('sylius.repository.product_taxon'),
        ])
        ->tag('form.type');

    $services->set('sylius.form.type.channel_price_history_config', 'Sylius\Bundle\CoreBundle\Form\Type\ChannelPriceHistoryConfigType')
        ->args([
            inline_service('\Symfony\Component\Form\Extension\Core\DataMapper\DataMapper'),
            '%sylius.model.channel_price_history_config.class%',
            '%sylius.form.type.channel_price_history_config.validation_groups%',
        ])
        ->tag('form.type');
};
