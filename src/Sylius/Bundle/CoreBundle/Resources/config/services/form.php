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

use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\FixedDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Calculator\PercentageDiscountPriceCalculator;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForProductScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForTaxonsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\CatalogPromotion\Checker\InForVariantsScopeVariantChecker;
use Sylius\Bundle\CoreBundle\Form\DataTransformer\ProductsToCodesTransformer;
use Sylius\Bundle\CoreBundle\Form\DataTransformer\ProductVariantsToCodesTransformer;
use Sylius\Bundle\CoreBundle\Form\DataTransformer\TaxonsToCodesTransformer;
use Sylius\Bundle\CoreBundle\Form\Extension\AddressTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\CartItemTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\CartTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\CatalogPromotionTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\ChannelTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\DisabledCsrfProtectionFormExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\OrderTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\PaymentMethodTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\ProductTranslationTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\ProductTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\ProductVariantGenerationTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\ProductVariantTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\PromotionCouponTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\PromotionFilterCollectionTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\PromotionTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\ShippingMethodTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\TaxonTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Extension\TaxRateTypeExtension;
use Sylius\Bundle\CoreBundle\Form\Type\AddressChoiceType;
use Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionAction\ChannelBasedFixedDiscountActionConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForProductsScopeConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForTaxonsScopeConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\CatalogPromotionScope\ForVariantsScopeConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\ChannelCollectionType;
use Sylius\Bundle\CoreBundle\Form\Type\ChannelPriceHistoryConfigType;
use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerCheckoutGuestType;
use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerGuestType;
use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerRegistrationType;
use Sylius\Bundle\CoreBundle\Form\Type\Customer\CustomerSimpleRegistrationType;
use Sylius\Bundle\CoreBundle\Form\Type\Order\AddToCartType;
use Sylius\Bundle\CoreBundle\Form\Type\Product\ChannelPricingType;
use Sylius\Bundle\CoreBundle\Form\Type\Product\ProductImageType;
use Sylius\Bundle\CoreBundle\Form\Type\Product\ProductReviewType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\ProductFilterConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Filter\TaxonFilterConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\ContainsProductConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\CustomerGroupConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\HasTaxonConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Promotion\Rule\TotalOfItemsFromTaxonConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Shipping\Calculator\ChannelBasedFlatRateConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\Shipping\Calculator\ChannelBasedPerUnitRateConfigurationType;
use Sylius\Bundle\CoreBundle\Form\Type\ShopBillingDataType;
use Sylius\Bundle\CoreBundle\Form\Type\TaxCalculationStrategyChoiceType;
use Sylius\Bundle\CoreBundle\Form\Type\Taxon\ProductTaxonAutocompleteChoiceType;
use Sylius\Bundle\CoreBundle\Form\Type\Taxon\TaxonImageType;
use Sylius\Bundle\CoreBundle\Form\Type\User\AdminUserType;
use Sylius\Bundle\CoreBundle\Form\Type\User\AvatarImageType;
use Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserRegistrationType;
use Sylius\Bundle\CoreBundle\Form\Type\User\ShopUserType;
use Sylius\Bundle\OrderBundle\Controller\AddToCartCommand;
use Sylius\Bundle\PromotionBundle\Form\Type\CatalogPromotionAction\PercentageDiscountActionConfigurationType;
use Symfony\Component\Form\Extension\Core\DataMapper\DataMapper;

return static function (ContainerConfigurator $container) {
    $parameters = $container->parameters();
    $services = $container->services();
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
    $parameters->set('sylius.catalog_promotion.action.fixed_discount', FixedDiscountPriceCalculator::TYPE);
    $parameters->set('sylius.catalog_promotion.action.percentage_discount', PercentageDiscountPriceCalculator::TYPE);
    $parameters->set('sylius.catalog_promotion.scope.for_products', InForProductScopeVariantChecker::TYPE);
    $parameters->set('sylius.catalog_promotion.scope.for_taxons', InForTaxonsScopeVariantChecker::TYPE);
    $parameters->set('sylius.catalog_promotion.scope.for_variants', InForVariantsScopeVariantChecker::TYPE);

    $services
        ->set('sylius.form.extension.type.address', AddressTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.channel', ChannelTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.order', OrderTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.cart', CartTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.cart_item', CartItemTypeExtension::class)
        ->args(['%sylius.order_item_quantity_modifier.limit%'])
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.catalog_promotion', CatalogPromotionTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.payment_method', PaymentMethodTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.tax_rate', TaxRateTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.taxon', TaxonTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.promotion', PromotionTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.promotion_coupon', PromotionCouponTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.promotion_filter_collection', PromotionFilterCollectionTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.shipping_method', ShippingMethodTypeExtension::class)
        ->args([service('sylius.validator.groups_generator.shipping_method_configuration')])
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.product', ProductTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.product_translation', ProductTranslationTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.product_variant', ProductVariantTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.product_variant_generation', ProductVariantGenerationTypeExtension::class)
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.extension.type.disabled_csrf_protection', DisabledCsrfProtectionFormExtension::class)
        ->args([service('service_container')])
        ->tag('form.type_extension', ['priority' => 100])
    ;

    $services
        ->set('sylius.form.type.product_review', ProductReviewType::class)
        ->args([
            '%sylius.model.product_review.class%',
            '%sylius.form.type.product_review.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.admin_user', AdminUserType::class)
        ->args([
            '%sylius.model.admin_user.class%',
            '%sylius.form.type.admin_user.validation_groups%',
            '%sylius_locale.locale%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shop_user', ShopUserType::class)
        ->args([
            '%sylius.model.shop_user.class%',
            '%sylius.form.type.shop_user.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shop_user_registration', ShopUserRegistrationType::class)
        ->args([
            '%sylius.model.shop_user.class%',
            '%sylius.form.type.shop_user_registration.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_image', ProductImageType::class)
        ->args([
            '%sylius.model.product_image.class%',
            '%sylius.model.product_variant.class%',
            '%sylius.form.type.product_image.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.taxon_image', TaxonImageType::class)
        ->args(['%sylius.model.taxon_image.class%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.avatar_image', AvatarImageType::class)
        ->args(['%sylius.model.avatar_image.class%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.catalog_promotion_action.percentage_discount_action_configuration', PercentageDiscountActionConfigurationType::class)
        ->tag('sylius.catalog_promotion.action_configuration_type', ['key' => '%sylius.catalog_promotion.action.percentage_discount%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.catalog_promotion_action.channel_based_fixed_discount_action_configuration', ChannelBasedFixedDiscountActionConfigurationType::class)
        ->tag('sylius.catalog_promotion.action_configuration_type', ['key' => '%sylius.catalog_promotion.action.fixed_discount%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.catalog_promotion_scope.for_products_scope_configuration', ForProductsScopeConfigurationType::class)
        ->args([service('sylius.form.type.data_transformer.products_to_codes')])
        ->tag('sylius.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_products%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.catalog_promotion_scope.for_taxons_scope_configuration', ForTaxonsScopeConfigurationType::class)
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('sylius.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_taxons%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.catalog_promotion_scope.for_variants_scope_configuration', ForVariantsScopeConfigurationType::class)
        ->args([service('sylius.form.type.data_transformer.product_variants_to_codes')])
        ->tag('sylius.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_variants%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.tax_calculation_strategy_choice', TaxCalculationStrategyChoiceType::class)
        ->args(['%sylius.tax_calculation_strategies%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_rule.customer_group_configuration', CustomerGroupConfigurationType::class)
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_rule.has_taxon_configuration', HasTaxonConfigurationType::class)
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_rule.total_of_items_from_taxon_configuration', TotalOfItemsFromTaxonConfigurationType::class)
        ->args([service('sylius.repository.taxon')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_rule.contains_product_configuration', ContainsProductConfigurationType::class)
        ->args([service('sylius.repository.product')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_action.filter.taxon', TaxonFilterConfigurationType::class)
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.promotion_action.filter.product', ProductFilterConfigurationType::class)
        ->args([service('sylius.form.type.data_transformer.products_to_codes')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.data_transformer.taxons_to_codes', TaxonsToCodesTransformer::class)
        ->args([service('sylius.repository.taxon')])
    ;

    $services
        ->set('sylius.form.type.data_transformer.product_variants_to_codes', ProductVariantsToCodesTransformer::class)
        ->args([service('sylius.repository.product_variant')])
    ;

    $services
        ->set('sylius.form.type.data_transformer.products_to_codes', ProductsToCodesTransformer::class)
        ->args([service('sylius.repository.product')])
    ;

    $services
        ->set('sylius.form.type.customer.guest', CustomerGuestType::class)
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer_guest.validation_groups%',
            service('sylius.repository.customer'),
            service('sylius.factory.customer'),
            service('sylius.canonicalizer'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.customer.checkout_guest', CustomerCheckoutGuestType::class)
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer_checkout_guest.validation_groups%',
            service('sylius.repository.customer'),
            service('sylius.factory.customer'),
            service('sylius.canonicalizer'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.customer.simple_registration', CustomerSimpleRegistrationType::class)
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer_simple_registration.validation_groups%',
            service('sylius.repository.customer'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.customer.registration', CustomerRegistrationType::class)
        ->args([
            '%sylius.model.customer.class%',
            '%sylius.form.type.customer_registration.validation_groups%',
            service('sylius.repository.customer'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.address_choice', AddressChoiceType::class)
        ->args([service('sylius.repository.address')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.order.add_to_cart', AddToCartType::class)
        ->args([
            AddToCartCommand::class,
            '%sylius.form.type.add_to_cart.validation_groups%',
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product.channel_pricing', ChannelPricingType::class)
        ->args([
            '%sylius.model.channel_pricing.class%',
            '%sylius.form.type.channel_pricing.validation_groups%',
            service('sylius.repository.channel_pricing'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.channels_collection', ChannelCollectionType::class)
        ->args([service('sylius.repository.channel')])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipping.calculator.channel_based_flat_rate_configuration', ChannelBasedFlatRateConfigurationType::class)
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shipping.calculator.channel_based_per_unit_rate_configuration', ChannelBasedPerUnitRateConfigurationType::class)
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.shop_billing_data', ShopBillingDataType::class)
        ->args(['%sylius.model.shop_billing_data.class%'])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.product_taxon_autocomplete_choice', ProductTaxonAutocompleteChoiceType::class)
        ->args([
            service('sylius.factory.product_taxon'),
            service('sylius.repository.product_taxon'),
        ])
        ->tag('form.type')
    ;

    $services
        ->set('sylius.form.type.channel_price_history_config', ChannelPriceHistoryConfigType::class)
        ->args([
            inline_service(DataMapper::class),
            '%sylius.model.channel_price_history_config.class%',
            '%sylius.form.type.channel_price_history_config.validation_groups%',
        ])
        ->tag('form.type')
    ;
};
