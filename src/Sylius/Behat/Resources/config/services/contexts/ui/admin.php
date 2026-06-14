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

use Sylius\Behat\Context\Ui\Admin\BrowsingCatalogPromotionProductVariantsContext;
use Sylius\Behat\Context\Ui\Admin\BrowsingProductVariantsContext;
use Sylius\Behat\Context\Ui\Admin\ChannelPricingLogEntryContext;
use Sylius\Behat\Context\Ui\Admin\DashboardContext;
use Sylius\Behat\Context\Ui\Admin\ErrorPageContext;
use Sylius\Behat\Context\Ui\Admin\ImpersonatingCustomersContext;
use Sylius\Behat\Context\Ui\Admin\LocaleContext;
use Sylius\Behat\Context\Ui\Admin\LoginContext;
use Sylius\Behat\Context\Ui\Admin\ManagingAdministratorLocalesContext;
use Sylius\Behat\Context\Ui\Admin\ManagingAdministratorsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingCatalogPromotionsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingChannelsBillingDataContext;
use Sylius\Behat\Context\Ui\Admin\ManagingChannelsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingCountriesContext;
use Sylius\Behat\Context\Ui\Admin\ManagingCurrenciesContext;
use Sylius\Behat\Context\Ui\Admin\ManagingCustomerGroupsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingCustomersContext;
use Sylius\Behat\Context\Ui\Admin\ManagingExchangeRatesContext;
use Sylius\Behat\Context\Ui\Admin\ManagingInventoryContext;
use Sylius\Behat\Context\Ui\Admin\ManagingLocalesContext;
use Sylius\Behat\Context\Ui\Admin\ManagingOrdersContext;
use Sylius\Behat\Context\Ui\Admin\ManagingPaymentMethodsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingPaymentRequestsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingPaymentsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingProductAssociationTypesContext;
use Sylius\Behat\Context\Ui\Admin\ManagingProductAttributesContext;
use Sylius\Behat\Context\Ui\Admin\ManagingProductOptionsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingProductReviewsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingProductsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingProductTaxonsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingProductVariantsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingProductVariantsPricesContext;
use Sylius\Behat\Context\Ui\Admin\ManagingPromotionCouponsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingPromotionsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingShipmentsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingShippingCategoriesContext;
use Sylius\Behat\Context\Ui\Admin\ManagingShippingMethodsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingTaxCategoriesContext;
use Sylius\Behat\Context\Ui\Admin\ManagingTaxonsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingTaxRateContext;
use Sylius\Behat\Context\Ui\Admin\ManagingTranslatableEntitiesContext;
use Sylius\Behat\Context\Ui\Admin\ManagingZonesContext;
use Sylius\Behat\Context\Ui\Admin\NavigatingBetweenProductShowAndEditPagesContext;
use Sylius\Behat\Context\Ui\Admin\NotificationContext;
use Sylius\Behat\Context\Ui\Admin\OrderHistoryContext;
use Sylius\Behat\Context\Ui\Admin\ProductCreationContext;
use Sylius\Behat\Context\Ui\Admin\ProductShowPageContext;
use Sylius\Behat\Context\Ui\Admin\ProductVariantsCreationContext;
use Sylius\Behat\Context\Ui\Admin\RemovingProductContext;
use Sylius\Behat\Context\Ui\Admin\RemovingTaxonContext;
use Sylius\Behat\Context\Ui\Admin\ResettingPasswordContext;
use Sylius\Behat\Context\Ui\Admin\SearchFilterContext;
use Sylius\Behat\Element\Admin\CatalogPromotion\FilterElement;
use Sylius\Behat\Element\Admin\CatalogPromotion\FormElement;
use Sylius\Behat\Element\Admin\Channel\DiscountedProductsCheckingPeriodInputElementInterface;
use Sylius\Behat\Element\Admin\Channel\ExcludeTaxonsFromShowingLowestPriceInputElementInterface;
use Sylius\Behat\Element\Admin\Channel\LowestPriceFlagElementInterface;
use Sylius\Behat\Element\Admin\Promotion\FormElementInterface;
use Sylius\Behat\Element\Admin\TaxRate\FilterElement as TaxRateFilterElement;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.behat.context.ui.admin.dashboard', DashboardContext::class)
        ->args([service('sylius.behat.page.admin.dashboard')])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.error_page', ErrorPageContext::class)
        ->args([service('sylius.behat.page.error')])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.locale', LocaleContext::class)
        ->args([
            service('sylius.behat.page.admin.dashboard'),
            service('translator'),
            service('sylius.behat.page.admin.administrator.create'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.login', LoginContext::class)
        ->args([
            service('sylius.behat.page.admin.dashboard'),
            service('sylius.behat.page.admin.login'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_administrators', ManagingAdministratorsContext::class)
        ->args([
            service('sylius.behat.page.admin.administrator.create'),
            service('sylius.behat.page.admin.administrator.index'),
            service('sylius.behat.page.admin.administrator.update'),
            service('sylius.behat.element.admin.top_bar'),
            service('sylius.behat.notification_checker.admin'),
            service('sylius.repository.admin_user'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_administrator_locale', ManagingAdministratorLocalesContext::class)
        ->args([
            service('sylius.behat.page.admin.administrator.update'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set(ManagingCatalogPromotionsContext::class)
        ->args([
            service('sylius.behat.page.admin.catalog_promotion.index'),
            service('sylius.behat.page.admin.catalog_promotion.create'),
            service('sylius.behat.page.admin.catalog_promotion.update'),
            service('sylius.behat.page.admin.catalog_promotion.show'),
            service(FormElement::class),
            service(FilterElement::class),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set(BrowsingCatalogPromotionProductVariantsContext::class)
        ->args([
            service('sylius.behat.page.admin.catalog_promotion.product_variant.index'),
            service('sylius.behat.page.admin.product.show_page'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_channels', ManagingChannelsContext::class)
        ->args([
            service('sylius.behat.page.admin.channel.index'),
            service('sylius.behat.page.admin.channel.create'),
            service('sylius.behat.page.admin.channel.update'),
            service('sylius.behat.element.admin.channel.shipping_address_in_checkout_required'),
            service('sylius.behat.current_page_resolver'),
            service('sylius.behat.notification_checker.admin'),
            service(DiscountedProductsCheckingPeriodInputElementInterface::class),
            service(LowestPriceFlagElementInterface::class),
            service(ExcludeTaxonsFromShowingLowestPriceInputElementInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_channels_billing_data', ManagingChannelsBillingDataContext::class)
        ->args([service('sylius.behat.element.admin.channel.shop_billing_data')])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_countries', ManagingCountriesContext::class)
        ->args([
            service('sylius.behat.page.admin.country.index'),
            service('sylius.behat.page.admin.country.create'),
            service('sylius.behat.page.admin.country.update'),
            service('sylius.behat.current_page_resolver'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_currencies', ManagingCurrenciesContext::class)
        ->args([
            service('sylius.behat.page.admin.currency.index'),
            service('sylius.behat.page.admin.currency.create'),
            service('sylius.behat.element.admin.currency.form'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_customers', ManagingCustomersContext::class)
        ->args([
            service('sylius.behat.page.admin.customer.create'),
            service('sylius.behat.page.admin.customer.index'),
            service('sylius.behat.page.admin.customer.update'),
            service('sylius.behat.page.admin.customer.show'),
            service('sylius.behat.page.admin.customer.order_index'),
            service('sylius.behat.current_page_resolver'),
            service('sylius.behat.element.admin.customer.form'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_customer_groups', ManagingCustomerGroupsContext::class)
        ->args([
            service('sylius.behat.page.admin.customer_group.create'),
            service('sylius.behat.page.admin.customer_group.index'),
            service('sylius.behat.page.admin.customer_group.update'),
            service('sylius.behat.element.admin.customer_group.form'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_exchange_rates', ManagingExchangeRatesContext::class)
        ->args([
            service('sylius.behat.page.admin.exchange_rate.create'),
            service('sylius.behat.page.admin.exchange_rate.index'),
            service('sylius.behat.page.admin.exchange_rate.update'),
            service('sylius.behat.element.admin.exchange_rate.form'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_inventory', ManagingInventoryContext::class)
        ->args([service('sylius.behat.page.admin.inventory.index')])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_locales', ManagingLocalesContext::class)
        ->args([
            service('sylius.behat.page.admin.locale.create'),
            service('sylius.behat.page.admin.locale.index'),
            service('sylius.behat.element.admin.locale.form'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_orders', ManagingOrdersContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.admin.order.index'),
            service('sylius.behat.page.admin.order.show'),
            service('sylius.behat.page.admin.order.update'),
            service('sylius.behat.page.error'),
            service('sylius.behat.notification_checker.admin'),
            service('sylius.behat.shared_security'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.order_history', OrderHistoryContext::class)
        ->args([service('sylius.behat.page.admin.order.history')])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_payments', ManagingPaymentsContext::class)
        ->args([
            service('sylius.behat.page.admin.payment.index'),
            service('sylius.behat.page.admin.order.show'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_shipments', ManagingShipmentsContext::class)
        ->args([
            service('sylius.behat.page.admin.shipment.index'),
            service('sylius.behat.page.admin.order.show'),
            service('sylius.behat.notification_checker.admin'),
            service('sylius.behat.page.admin.shipment.show'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_payment_methods', ManagingPaymentMethodsContext::class)
        ->args([
            service('sylius.behat.page.admin.payment_method.create'),
            service('sylius.behat.page.admin.payment_method.index'),
            service('sylius.behat.page.admin.payment_method.update'),
            service('sylius.behat.current_page_resolver'),
            ['offline' => 'Offline'],
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.product_showpage', ProductShowPageContext::class)
        ->args([
            service('sylius.behat.page.admin.product.index'),
            service('sylius.behat.page.admin.product.show_page'),
            service('sylius.behat.element.product.show.associations'),
            service('sylius.behat.element.product.show.attributes'),
            service('sylius.behat.element.product.show.details'),
            service('sylius.behat.element.product.show.media'),
            service('sylius.behat.element.product.show.more_details'),
            service('sylius.behat.element.product.show.pricing'),
            service('sylius.behat.element.product.show.shipping'),
            service('sylius.behat.element.product.show.taxonomy'),
            service('sylius.behat.element.product.show.options'),
            service('sylius.behat.element.product.show.variants'),
            service('router'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_products', ManagingProductsContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.admin.product.create_simple'),
            service('sylius.behat.page.admin.product.create_configurable'),
            service('sylius.behat.page.admin.product.index'),
            service('sylius.behat.page.admin.product.update_simple'),
            service('sylius.behat.page.admin.product.update_configurable'),
            service('sylius.behat.page.admin.product_review.index'),
            service('sylius.behat.page.admin.product.index_per_taxon'),
            service('sylius.behat.page.admin.product_variant.create'),
            service('sylius.behat.page.admin.product_variant.generate'),
            service('sylius.behat.current_page_resolver'),
            service('sylius.behat.notification_checker.admin'),
            service('sylius.behat.page.admin.product_variant.update'),
            service('sylius.behat.java_script_test_helper'),
            service('sylius.behat.element.admin.product.association_form'),
            service('sylius.behat.element.admin.product.attributes_form'),
            service('sylius.behat.element.admin.product.channel_pricing_form'),
            service('sylius.behat.element.admin.product.media_form'),
            service('sylius.behat.element.admin.product.taxonomy_form'),
            service('sylius.behat.element.admin.product.translations_form'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_product_association_types', ManagingProductAssociationTypesContext::class)
        ->args([
            service('sylius.behat.page.admin.product_association_type.create'),
            service('sylius.behat.page.admin.product_association_type.index'),
            service('sylius.behat.page.admin.product_association_type.update'),
            service('sylius.behat.element.admin.product_association_type.form'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_product_attributes', ManagingProductAttributesContext::class)
        ->args([
            service('sylius.behat.page.admin.product_attribute.create'),
            service('sylius.behat.page.admin.product_attribute.index'),
            service('sylius.behat.page.admin.product_attribute.update'),
            service('sylius.behat.element.admin.product_attribute.form'),
            service('sylius.behat.element.admin.product_attribute.filter'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_product_options', ManagingProductOptionsContext::class)
        ->args([
            service('sylius.behat.page.admin.product_option.index'),
            service('sylius.behat.page.admin.product_option.create'),
            service('sylius.behat.page.admin.product_option.update'),
            service('sylius.behat.element.admin.product_option.form'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_product_reviews', ManagingProductReviewsContext::class)
        ->args([
            service('sylius.behat.page.admin.product_review.index'),
            service('sylius.behat.page.admin.product_review.update'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set(ManagingProductTaxonsContext::class)
        ->args([
            service('sylius.behat.page.admin.product.update_simple'),
            service('sylius.behat.element.admin.product.taxonomy_form'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_product_variants', ManagingProductVariantsContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.admin.product_variant.create'),
            service('sylius.behat.page.admin.product_variant.index'),
            service('sylius.behat.page.admin.product_variant.update'),
            service('sylius.behat.page.admin.product_variant.generate'),
            service('sylius.behat.current_page_resolver'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set(ManagingProductVariantsPricesContext::class)
        ->args([service('sylius.behat.page.admin.product_variant.update')])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.browsing_product_variants', BrowsingProductVariantsContext::class)
        ->args([
            service('sylius.behat.page.admin.product_variant.index'),
            service('sylius.resolver.product_variant'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_promotions', ManagingPromotionsContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.admin.promotion.index'),
            service('sylius.behat.page.admin.promotion_coupon.index'),
            service('sylius.behat.page.admin.promotion.create'),
            service('sylius.behat.page.admin.promotion.update'),
            service('sylius.behat.current_page_resolver'),
            service('sylius.behat.notification_checker.admin'),
            service(FormElementInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_promotion_coupons', ManagingPromotionCouponsContext::class)
        ->args([
            service('sylius.behat.page.admin.promotion_coupon.create'),
            service('sylius.behat.page.admin.promotion_coupon.generate'),
            service('sylius.behat.page.admin.promotion_coupon.index'),
            service('sylius.behat.page.admin.promotion_coupon.update'),
            service('sylius.behat.element.admin.promotion_coupon.form'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_shipping_methods', ManagingShippingMethodsContext::class)
        ->args([
            service('sylius.behat.page.admin.shipping_method.index'),
            service('sylius.behat.page.admin.shipping_method.create'),
            service('sylius.behat.page.admin.shipping_method.update'),
            service('sylius.behat.element.admin.shipping_method.form'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_shipping_categories', ManagingShippingCategoriesContext::class)
        ->args([
            service('sylius.behat.page.admin.shipping_category.index'),
            service('sylius.behat.page.admin.shipping_category.create'),
            service('sylius.behat.page.admin.shipping_category.update'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_tax_categories', ManagingTaxCategoriesContext::class)
        ->args([
            service('sylius.behat.page.admin.tax_category.index'),
            service('sylius.behat.page.admin.tax_category.create'),
            service('sylius.behat.page.admin.tax_category.update'),
            service('sylius.behat.element.admin.tax_category.form'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_translatable_entities', ManagingTranslatableEntitiesContext::class)
        ->args([
            service('sylius.behat.page.admin.taxon.create'),
            service('sylius.behat.element.admin.taxon.form'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_taxons', ManagingTaxonsContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.admin.taxon.create'),
            service('sylius.behat.page.admin.taxon.create_for_parent'),
            service('sylius.behat.page.admin.taxon.update'),
            service('sylius.behat.element.admin.taxon.form'),
            service('sylius.behat.element.admin.taxon.image_form'),
            service('sylius.behat.element.admin.taxon.tree'),
            service('sylius.behat.notification_checker.admin'),
            service('sylius.behat.java_script_test_helper'),
            service('sylius.behat.page.admin.product.update_simple'),
            service('sylius.behat.element.admin.product.taxonomy_form'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_tax_rate', ManagingTaxRateContext::class)
        ->args([
            service('sylius.behat.page.admin.tax_rate.index'),
            service('sylius.behat.page.admin.tax_rate.create'),
            service('sylius.behat.page.admin.tax_rate.update'),
            service('sylius.behat.current_page_resolver'),
            service(TaxRateFilterElement::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_zones', ManagingZonesContext::class)
        ->args([
            service('sylius.behat.page.admin.zone.index'),
            service('sylius.behat.page.admin.zone.create'),
            service('sylius.behat.page.admin.zone.update'),
            service('sylius.behat.element.admin.zone.form'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.notification', NotificationContext::class)
        ->args([service('sylius.behat.element.admin.notifications')])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.impersonating_customers', ImpersonatingCustomersContext::class)
        ->args([
            service('sylius.behat.page.admin.customer.show'),
            service('sylius.behat.page.admin.dashboard'),
            service('sylius.behat.page.shop.home'),
            service('sylius.behat.page.admin.impersonate_user'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set(RemovingProductContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.admin.product.index'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.removing_taxons', RemovingTaxonContext::class)
        ->args([
            service('sylius.behat.page.admin.taxon.create'),
            service('sylius.behat.element.admin.taxon.tree'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.resetting_password', ResettingPasswordContext::class)
        ->args([
            service('sylius.behat.page.admin.request_password_reset'),
            service('sylius.behat.page.admin.reset_password'),
            service('sylius.behat.element.admin.account.reset'),
            service('sylius.behat.notification_checker.admin'),
        ])
    ;

    $services
        ->set(
            'sylius.behat.context.ui.admin.navigating_between_product_show_and_edit_pages_context',
            NavigatingBetweenProductShowAndEditPagesContext::class,
        )
        ->args([
            service('sylius.behat.page.admin.product.update_simple'),
            service('sylius.behat.page.admin.product_variant.update'),
            service('sylius.behat.page.admin.product.show_page'),
            service('sylius.behat.page.admin.product.create_simple'),
            service('sylius.behat.page.admin.product.create_configurable'),
        ])
    ;

    $services
        ->set(ProductCreationContext::class)
        ->args([
            service('sylius.behat.page.admin.product.create_simple'),
            service('sylius.behat.element.admin.product.translations_form'),
            service('sylius.behat.element.admin.product.channel_pricing_form'),
            service('sylius.behat.element.admin.product.taxonomy_form'),
            service('slugger'),
        ])
    ;

    $services
        ->set(ProductVariantsCreationContext::class)
        ->args([service('sylius.behat.page.admin.product_variant.create')])
    ;

    $services
        ->set(ChannelPricingLogEntryContext::class)
        ->args([service('sylius.behat.page.admin.channel_pricing_log_entry.index')])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.search_filter', SearchFilterContext::class)
        ->args([service('sylius.behat.element.admin.crud.index.search_filter')])
    ;

    $services
        ->set('sylius.behat.context.ui.admin.managing_payment_requests', ManagingPaymentRequestsContext::class)
        ->args([
            service('sylius.behat.page.admin.payment.payment_request.index'),
            service('sylius.behat.page.admin.payment.payment_request.show'),
            service('sylius.repository.payment_request'),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.shared_security'),
        ])
    ;
};
