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

use Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Action\ProductFilterConfigurationTypeExtension;
use Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Action\TaxonFilterConfigurationTypeExtension;
use Sylius\Bundle\AdminBundle\Form\Extension\Promotion\PromotionActionTypeExtension;
use Sylius\Bundle\AdminBundle\Form\Extension\Promotion\PromotionRuleTypeExtension;
use Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Rule\ContainsProductConfigurationTypeExtension;
use Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Rule\HasTaxonConfigurationTypeExtension;
use Sylius\Bundle\AdminBundle\Form\Extension\Promotion\Rule\TotalOfItemsFromTaxonConfigurationTypeExtension;
use Sylius\Bundle\AdminBundle\Form\Extension\PromotionTypeExtension;
use Sylius\Bundle\AdminBundle\Form\RequestPasswordResetType;
use Sylius\Bundle\AdminBundle\Form\Type\AddressType;
use Sylius\Bundle\AdminBundle\Form\Type\AdminUserType;
use Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionActionType;
use Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScope\ForProductsScopeConfigurationType;
use Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScope\ForTaxonsScopeConfigurationType;
use Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScope\ForVariantsScopeConfigurationType;
use Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionScopeType;
use Sylius\Bundle\AdminBundle\Form\Type\CatalogPromotionType;
use Sylius\Bundle\AdminBundle\Form\Type\ChannelPriceHistoryConfigType;
use Sylius\Bundle\AdminBundle\Form\Type\ChannelType;
use Sylius\Bundle\AdminBundle\Form\Type\CountryType;
use Sylius\Bundle\AdminBundle\Form\Type\CurrencyType;
use Sylius\Bundle\AdminBundle\Form\Type\CustomerGroupType;
use Sylius\Bundle\AdminBundle\Form\Type\CustomerType;
use Sylius\Bundle\AdminBundle\Form\Type\ExchangeRateType;
use Sylius\Bundle\AdminBundle\Form\Type\Grid\Filter\UxAutocompleteFilterType;
use Sylius\Bundle\AdminBundle\Form\Type\Grid\Filter\UxTranslatableAutocompleteFilterType;
use Sylius\Bundle\AdminBundle\Form\Type\LocaleType;
use Sylius\Bundle\AdminBundle\Form\Type\OrderType;
use Sylius\Bundle\AdminBundle\Form\Type\PaymentMethodType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductAssociationTypeType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductAttributeAutocompleteType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductAttributeType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductAutocompleteType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductGenerateVariantsType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductImageType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductOptionType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductReviewType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductVariantAutocompleteType;
use Sylius\Bundle\AdminBundle\Form\Type\ProductVariantType;
use Sylius\Bundle\AdminBundle\Form\Type\PromotionCouponGeneratorInstructionType;
use Sylius\Bundle\AdminBundle\Form\Type\PromotionCouponType;
use Sylius\Bundle\AdminBundle\Form\Type\PromotionType;
use Sylius\Bundle\AdminBundle\Form\Type\ResetPasswordType;
use Sylius\Bundle\AdminBundle\Form\Type\ShipmentShipType;
use Sylius\Bundle\AdminBundle\Form\Type\ShippingCategoryType;
use Sylius\Bundle\AdminBundle\Form\Type\ShippingMethodRuleType;
use Sylius\Bundle\AdminBundle\Form\Type\ShippingMethodType;
use Sylius\Bundle\AdminBundle\Form\Type\ShopUserType;
use Sylius\Bundle\AdminBundle\Form\Type\TaxCategoryType;
use Sylius\Bundle\AdminBundle\Form\Type\TaxonAutocompleteType;
use Sylius\Bundle\AdminBundle\Form\Type\TaxonType;
use Sylius\Bundle\AdminBundle\Form\Type\TaxRateType;
use Sylius\Bundle\AdminBundle\Form\Type\TranslatableAutocompleteType;
use Sylius\Bundle\AdminBundle\Form\Type\ZoneType;
use Symfony\Component\Form\Extension\Core\DataMapper\DataMapper;

return static function (ContainerConfigurator $container) {
    $services = $container->services();
    $parameters = $container->parameters();
    $parameters->set('sylius.form.type.product_review.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.admin.password_reset_request.validation_groups', ['sylius']);
    $parameters->set('sylius.form.type.admin.reset_password.validation_groups', ['sylius']);

    $services->set('sylius_admin.form.type.request_password_reset', RequestPasswordResetType::class)
        ->args(['%sylius.form.type.admin.password_reset_request.validation_groups%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.reset_password', ResetPasswordType::class)
        ->args(['%sylius.form.type.admin.reset_password.validation_groups%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.grid_filter.ux_autocomplete', UxAutocompleteFilterType::class)
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.type.grid_filter.ux_translatable_autocomplete', UxTranslatableAutocompleteFilterType::class)
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.extension.type.promotion', PromotionTypeExtension::class)
        ->args([
            '%sylius.promotion_rules%',
            '%sylius.promotion_actions%',
        ])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion.promotion_action', PromotionActionTypeExtension::class)
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion_action.product_filter_configuration', ProductFilterConfigurationTypeExtension::class)
        ->args([service('sylius.form.type.data_transformer.products_to_codes')])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion_action.taxon_filter_configuration', TaxonFilterConfigurationTypeExtension::class)
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion.promotion_rule', PromotionRuleTypeExtension::class)
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion_rule.contains_product_configuration', ContainsProductConfigurationTypeExtension::class)
        ->args([service('sylius.repository.product')])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion_rule.has_taxon_configuration', HasTaxonConfigurationTypeExtension::class)
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.extension.type.promotion_rule.total_of_items_from_taxon_configuration', TotalOfItemsFromTaxonConfigurationTypeExtension::class)
        ->args([service('sylius.repository.taxon')])
        ->tag('form.type_extension');

    $services->set('sylius_admin.form.type.admin_user', AdminUserType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.address', AddressType::class)
        ->args([service('sylius.repository.country')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion', CatalogPromotionType::class)
        ->args([
            '%sylius.catalog_promotion.scopes_types%',
            '%sylius.catalog_promotion.actions_types%',
        ])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion_action', CatalogPromotionActionType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion_scope', CatalogPromotionScopeType::class)
        ->args([tagged_iterator('sylius_admin.catalog_promotion.scope_configuration_type', indexAttribute: 'key')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion_scope.for_products_scope_configuration', ForProductsScopeConfigurationType::class)
        ->args([service('sylius.form.type.data_transformer.products_to_codes')])
        ->tag('sylius_admin.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_products%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion_scope.for_taxons_scope_configuration', ForTaxonsScopeConfigurationType::class)
        ->args([service('sylius.form.type.data_transformer.taxons_to_codes')])
        ->tag('sylius_admin.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_taxons%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.catalog_promotion_scope.for_variants_scope_configuration', ForVariantsScopeConfigurationType::class)
        ->args([service('sylius.form.type.data_transformer.product_variants_to_codes')])
        ->tag('sylius_admin.catalog_promotion.scope_configuration_type', ['key' => '%sylius.catalog_promotion.scope.for_variants%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.channel', ChannelType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.channel_price_history_config', ChannelPriceHistoryConfigType::class)
        ->args([
            service('sylius.repository.taxon'),
            inline_service(DataMapper::class),
        ])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.country', CountryType::class)
        ->args([service('sylius.repository.country')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.currency', CurrencyType::class)
        ->args([service('sylius.repository.currency')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.customer', CustomerType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.customer_group', CustomerGroupType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.exchange_rate', ExchangeRateType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.locale', LocaleType::class)
        ->args([service('sylius.repository.locale')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.order', OrderType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.payment_method', PaymentMethodType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product', ProductType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_association_type', ProductAssociationTypeType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_attribute', ProductAttributeType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_attribute_autocomplete', ProductAttributeAutocompleteType::class)
        ->args(['%sylius.model.product_attribute.class%'])
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.type.product_autocomplete', ProductAutocompleteType::class)
        ->args(['%sylius.model.product.class%'])
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.type.product_generate_variants', ProductGenerateVariantsType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_image', ProductImageType::class)
        ->args(['%sylius.model.product_variant.class%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_option', ProductOptionType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_review', ProductReviewType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_variant', ProductVariantType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.product_variant_autocomplete', ProductVariantAutocompleteType::class)
        ->args(['%sylius.model.product_variant.class%'])
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.type.promotion', PromotionType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.promotion_coupon', PromotionCouponType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.promotion_coupon_generator_instruction', PromotionCouponGeneratorInstructionType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.shipping_category', ShippingCategoryType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.shipping_method', ShippingMethodType::class)
        ->args(['%sylius.shipping_method_rules%'])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.shipping_method_rule', ShippingMethodRuleType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.shipment_ship', ShipmentShipType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.shop_user', ShopUserType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.tax_category', TaxCategoryType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.taxon', TaxonType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.taxon_autocomplete', TaxonAutocompleteType::class)
        ->args(['%sylius.model.taxon.class%'])
        ->tag('form.type')
        ->tag('ux.entity_autocomplete_field');

    $services->set('sylius_admin.form.type.tax_rate', TaxRateType::class)
        ->tag('form.type');

    $services->set('sylius_admin.form.type.translatable_autocomplete', TranslatableAutocompleteType::class)
        ->args([service('sylius.context.locale')])
        ->tag('form.type');

    $services->set('sylius_admin.form.type.zone', ZoneType::class)
        ->tag('form.type');
};
