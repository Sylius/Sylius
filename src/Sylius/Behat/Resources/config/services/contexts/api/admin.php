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

use Sylius\Behat\Client\ResponseCheckerInterface;
use Sylius\Behat\Context\Api\Admin\BrowsingCatalogPromotionProductVariantsContext;
use Sylius\Behat\Context\Api\Admin\BrowsingProductVariantsContext;
use Sylius\Behat\Context\Api\Admin\ChannelPricingLogEntryContext;
use Sylius\Behat\Context\Api\Admin\CreatingProductVariantContext;
use Sylius\Behat\Context\Api\Admin\DashboardContext;
use Sylius\Behat\Context\Api\Admin\LoginContext;
use Sylius\Behat\Context\Api\Admin\ManagingAdministratorsContext;
use Sylius\Behat\Context\Api\Admin\ManagingCatalogPromotionsContext;
use Sylius\Behat\Context\Api\Admin\ManagingChannelPriceHistoryConfigContext;
use Sylius\Behat\Context\Api\Admin\ManagingChannelsBillingDataContext;
use Sylius\Behat\Context\Api\Admin\ManagingChannelsContext;
use Sylius\Behat\Context\Api\Admin\ManagingCountriesContext;
use Sylius\Behat\Context\Api\Admin\ManagingCurrenciesContext;
use Sylius\Behat\Context\Api\Admin\ManagingCustomerGroupsContext;
use Sylius\Behat\Context\Api\Admin\ManagingCustomersContext;
use Sylius\Behat\Context\Api\Admin\ManagingExchangeRatesContext;
use Sylius\Behat\Context\Api\Admin\ManagingLocalesContext;
use Sylius\Behat\Context\Api\Admin\ManagingOrdersContext;
use Sylius\Behat\Context\Api\Admin\ManagingPaymentMethodsContext;
use Sylius\Behat\Context\Api\Admin\ManagingPaymentRequestsContext;
use Sylius\Behat\Context\Api\Admin\ManagingPaymentsContext;
use Sylius\Behat\Context\Api\Admin\ManagingPlacedOrderAddressesContext;
use Sylius\Behat\Context\Api\Admin\ManagingProductAssociationsContext;
use Sylius\Behat\Context\Api\Admin\ManagingProductAssociationTypesContext;
use Sylius\Behat\Context\Api\Admin\ManagingProductAttributesContext;
use Sylius\Behat\Context\Api\Admin\ManagingProductImagesContext;
use Sylius\Behat\Context\Api\Admin\ManagingProductOptionsContext;
use Sylius\Behat\Context\Api\Admin\ManagingProductReviewsContext;
use Sylius\Behat\Context\Api\Admin\ManagingProductsContext;
use Sylius\Behat\Context\Api\Admin\ManagingProductTaxonsContext;
use Sylius\Behat\Context\Api\Admin\ManagingProductVariantsContext;
use Sylius\Behat\Context\Api\Admin\ManagingProductVariantsPricesContext;
use Sylius\Behat\Context\Api\Admin\ManagingPromotionCouponsContext;
use Sylius\Behat\Context\Api\Admin\ManagingPromotionsContext;
use Sylius\Behat\Context\Api\Admin\ManagingShipmentsContext;
use Sylius\Behat\Context\Api\Admin\ManagingShippingCategoriesContext;
use Sylius\Behat\Context\Api\Admin\ManagingShippingMethodsContext;
use Sylius\Behat\Context\Api\Admin\ManagingTaxCategoriesContext;
use Sylius\Behat\Context\Api\Admin\ManagingTaxonImagesContext;
use Sylius\Behat\Context\Api\Admin\ManagingTaxonsContext;
use Sylius\Behat\Context\Api\Admin\ManagingTaxRatesContext;
use Sylius\Behat\Context\Api\Admin\ManagingZonesContext;
use Sylius\Behat\Context\Api\Admin\RemovingProductContext;
use Sylius\Behat\Context\Api\Admin\RemovingTaxonContext;
use Sylius\Behat\Context\Api\Admin\ResettingPasswordContext;
use Sylius\Behat\Context\Api\Admin\TranslationContext;
use Sylius\Behat\Service\Converter\IriConverter;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.behat.context.api.admin.login', LoginContext::class)
        ->args([
            service('sylius.behat.client.admin_api_platform_security_client'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.translation', TranslationContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set(BrowsingCatalogPromotionProductVariantsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.iri_converter'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.browsing_product_variant', BrowsingProductVariantsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.creating_product_variant', CreatingProductVariantContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service('api_platform.iri_converter'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_administrators', ManagingAdministratorsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('behat.mink.parameters'),
            service('translator'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_product_taxons', ManagingProductTaxonsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service('api_platform.iri_converter'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_channels', ManagingChannelsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.symfony.iri_converter'),
        ])
    ;

    $services
        ->set(ManagingChannelsBillingDataContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_countries', ManagingCountriesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('api_platform.iri_converter'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_currencies', ManagingCurrenciesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_exchange_rates', ManagingExchangeRatesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            '%sylius.security.api_route%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_locales', ManagingLocalesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_product_associations', ManagingProductAssociationsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service(IriConverter::class),
            service('sylius.repository.product_association'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_product_association_types', ManagingProductAssociationTypesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_product_attributes', ManagingProductAttributesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_product_images', ManagingProductImagesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('behat.mink.parameters'),
            service(IriConverter::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_product_options', ManagingProductOptionsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('api_platform.iri_converter'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_products', ManagingProductsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service(IriConverter::class),
            service('sylius.behat.shared_storage'),
            '%sylius.security.api_route%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_product_variants', ManagingProductVariantsContext::class)
        ->args([
            service('sylius.resolver.product_variant'),
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.symfony.iri_converter'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_product_variants_prices', ManagingProductVariantsPricesContext::class)
        ->args([service('sylius.behat.api_platform_client.admin')])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_tax_categories', ManagingTaxCategoriesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_taxons', ManagingTaxonsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service(IriConverter::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_taxon_images', ManagingTaxonImagesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('behat.mink.parameters'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_shipping_categories', ManagingShippingCategoriesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_shipping_methods', ManagingShippingMethodsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service(IriConverter::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_product_reviews', ManagingProductReviewsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service(IriConverter::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_payments', ManagingPaymentsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.symfony.iri_converter'),
            '%sylius.security.api_route%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_shipments', ManagingShipmentsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service(IriConverter::class),
            service('sylius.behat.shared_storage'),
            '%sylius.security.api_route%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_orders', ManagingOrdersContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.iri_converter'),
            service('sylius.behat.api_admin_security'),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.api.shared_security'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_payment_methods', ManagingPaymentMethodsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.iri_converter'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_zones', ManagingZonesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.removing_product', RemovingProductContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set(RemovingTaxonContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_promotions', ManagingPromotionsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.iri_converter'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_catalog_promotions', ManagingCatalogPromotionsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.symfony.iri_converter'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_tax_rates', ManagingTaxRatesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.iri_converter'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.resetting_password', ResettingPasswordContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service('sylius.behat.request_factory'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            '%sylius.security.api_route%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.channel_pricing_log_entry', ChannelPricingLogEntryContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_customers', ManagingCustomersContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.iri_converter'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_customer_groups', ManagingCustomerGroupsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_placed_order_addresses', ManagingPlacedOrderAddressesContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_promotion_coupons', ManagingPromotionCouponsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service('sylius.behat.request_factory'),
            service(ResponseCheckerInterface::class),
            service('api_platform.symfony.iri_converter'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.dashboard_context', DashboardContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.clock'),
        ])
    ;

    $services
        ->set(ManagingChannelPriceHistoryConfigContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.symfony.iri_converter'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.admin.managing_payment_requests', ManagingPaymentRequestsContext::class)
        ->args([
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('api_platform.symfony.iri_converter'),
            service('sylius.repository.payment_request'),
            service('sylius.behat.request_factory'),
            service('sylius.behat.api.shared_security'),
            service('sylius.behat.shared_storage'),
        ])
    ;
};
