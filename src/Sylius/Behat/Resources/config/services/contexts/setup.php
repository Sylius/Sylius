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

use Sylius\Behat\Context\Setup\AddressContext;
use Sylius\Behat\Context\Setup\AdminSecurityContext;
use Sylius\Behat\Context\Setup\AdminUserContext;
use Sylius\Behat\Context\Setup\CalendarContext;
use Sylius\Behat\Context\Setup\CartContext;
use Sylius\Behat\Context\Setup\CatalogPromotionContext;
use Sylius\Behat\Context\Setup\ChannelContext;
use Sylius\Behat\Context\Setup\Checkout\AddressContext as CheckoutAddressContext;
use Sylius\Behat\Context\Setup\Checkout\PaymentContext as CheckoutPaymentContext;
use Sylius\Behat\Context\Setup\Checkout\ShippingContext as CheckoutShippingContext;
use Sylius\Behat\Context\Setup\CheckoutContext;
use Sylius\Behat\Context\Setup\CurrencyContext;
use Sylius\Behat\Context\Setup\CustomerContext;
use Sylius\Behat\Context\Setup\CustomerGroupContext;
use Sylius\Behat\Context\Setup\ExchangeRateContext;
use Sylius\Behat\Context\Setup\GeographicalContext;
use Sylius\Behat\Context\Setup\LocaleContext;
use Sylius\Behat\Context\Setup\OrderContext;
use Sylius\Behat\Context\Setup\PaymentContext;
use Sylius\Behat\Context\Setup\PaymentRequestContext;
use Sylius\Behat\Context\Setup\PriceHistoryContext;
use Sylius\Behat\Context\Setup\ProductAssociationContext;
use Sylius\Behat\Context\Setup\ProductAttributeContext;
use Sylius\Behat\Context\Setup\ProductContext;
use Sylius\Behat\Context\Setup\ProductOptionContext;
use Sylius\Behat\Context\Setup\ProductReviewContext;
use Sylius\Behat\Context\Setup\ProductTaxonContext;
use Sylius\Behat\Context\Setup\PromotionContext;
use Sylius\Behat\Context\Setup\ShippingCategoryContext;
use Sylius\Behat\Context\Setup\ShippingContext;
use Sylius\Behat\Context\Setup\ShopSecurityContext;
use Sylius\Behat\Context\Setup\TaxationContext;
use Sylius\Behat\Context\Setup\TaxonomyContext;
use Sylius\Behat\Context\Setup\ThemeContext;
use Sylius\Behat\Context\Setup\UserContext;
use Sylius\Behat\Context\Setup\ZoneContext;
use Sylius\Bundle\ThemeBundle\Configuration\Test\TestThemeConfigurationManagerInterface;
use Sylius\Resource\Generator\RandomnessGeneratorInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.behat.context.setup.address', AddressContext::class)
        ->args([
            service('sylius.repository.address'),
            service('sylius.manager.customer'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.admin_user', AdminUserContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.fixture.example_factory.admin_user'),
            service('sylius.repository.admin_user'),
            service('sylius.uploader.image'),
            service('doctrine.orm.entity_manager'),
            service('behat.mink.parameters'),
            service('sylius.factory.avatar_image'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.calendar', CalendarContext::class)
        ->args(['%sylius.behat.clock.date_file%'])
    ;

    $services
        ->set('sylius.behat.context.setup.cart', CartContext::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.command_bus'),
            service('sylius.resolver.product_variant'),
            service('sylius.random_generator'),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.context.setup.checkout.address'),
            service('sylius.behat.context.setup.checkout.shipping'),
            service('sylius.behat.context.setup.checkout.payment'),
            '%sylius.behat.guest_cart_token_file%',
            service('security.token_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.checkout', CheckoutContext::class)
        ->args([
            service('sylius.repository.order'),
            service('sylius.repository.shipping_method'),
            service('sylius.repository.payment_method'),
            service('sylius.command_bus'),
            service('sylius.factory.address'),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.context.setup.checkout.shipping'),
            service('sylius.behat.context.setup.checkout.payment'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.checkout.address', CheckoutAddressContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.command_bus'),
            service('sylius.behat.factory.address'),
            service('sylius.converter.country_name'),
        ])
    ;

    $services
        ->set(
            'sylius.behat.context.setup.checkout.shipping',
            CheckoutShippingContext::class,
        )
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.command_bus'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.checkout.payment', CheckoutPaymentContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.command_bus'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.channel', ChannelContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.channel_context_setter'),
            service('sylius.behat.factory.default_united_states_channel'),
            service('sylius.behat.factory.default_channel'),
            service('sylius.repository.channel'),
            service('sylius.manager.channel'),
            service('sylius.factory.shop_billing_data'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.currency', CurrencyContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.currency'),
            service('sylius.factory.currency'),
            service('sylius.manager.channel'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.customer', CustomerContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.customer'),
            service('sylius.manager.customer'),
            service('sylius.factory.customer'),
            service('sylius.factory.shop_user'),
            service('sylius.factory.address'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.customer_group', CustomerGroupContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.customer_group'),
            service('sylius.factory.customer_group'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.exchange_rate', ExchangeRateContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.factory.exchange_rate'),
            service('sylius.repository.exchange_rate'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.geographical', GeographicalContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.factory.country'),
            service('sylius.factory.province'),
            service('sylius.repository.country'),
            service('sylius.converter.country_name'),
            service('sylius.manager.province'),
            service('sylius.repository.province'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.locale', LocaleContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.converter.locale'),
            service('sylius.factory.locale'),
            service('sylius.repository.locale'),
            service('sylius.manager.locale'),
            service('sylius.manager.channel'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.order', OrderContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.factory.order'),
            service('sylius.factory.address'),
            service('sylius.factory.customer'),
            service('sylius.factory.order_item'),
            service('sylius.factory.shipment'),
            service('sylius_abstraction.state_machine'),
            service('sylius.repository.country'),
            service('sylius.repository.customer'),
            service('sylius.repository.order'),
            service('sylius.repository.payment_method'),
            service('sylius.repository.shipping_method'),
            service('sylius.resolver.product_variant'),
            service('sylius.modifier.order_item_quantity'),
            service('doctrine.orm.entity_manager'),
            service('sylius.behat.clock'),
            service(RandomnessGeneratorInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.payment', PaymentContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.payment_method'),
            service('sylius.fixture.example_factory.payment_method'),
            service('sylius.factory.payment_method_translation'),
            service('sylius.manager.payment_method'),
            ['offline' => 'Offline'],
        ])
    ;

    $services
        ->set(PriceHistoryContext::class)
        ->args([
            service('sylius.behat.context.setup.calendar'),
            service('sylius.manager.channel_pricing'),
            service('sylius.resolver.product_variant'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.product', ProductContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.product'),
            service('sylius.factory.product'),
            service('sylius.factory.product_translation'),
            service('sylius.factory.product_variant'),
            service('sylius.factory.product_variant_translation'),
            service('sylius.factory.channel_pricing'),
            service('sylius.factory.product_option'),
            service('sylius.factory.product_option_value'),
            service('sylius.factory.product_image'),
            service('sylius.factory.product_taxon'),
            service('doctrine.orm.entity_manager'),
            service('sylius.generator.product_variant'),
            service('sylius.repository.product_variant'),
            service('sylius.resolver.product_variant'),
            service('sylius.uploader.image'),
            service('sylius.generator.slug'),
            service('behat.mink.parameters'),
            service('sylius.event_bus'),
            service('sylius.behat.context.setup.product_taxon'),
        ])
    ;

    $services
        ->set(
            'sylius.behat.context.setup.product_association',
            ProductAssociationContext::class,
        )
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.factory.product_association_type'),
            service('sylius.factory.product_association_type_translation'),
            service('sylius.factory.product_association'),
            service('sylius.repository.product_association_type'),
            service('sylius.repository.product_association'),
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services
        ->set(
            'sylius.behat.context.setup.product_attribute',
            ProductAttributeContext::class,
        )
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.product_attribute'),
            service('sylius.factory.product_attribute'),
            service('sylius.factory.product_attribute_value'),
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.product_option', ProductOptionContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.product_option'),
            service('sylius.factory.product_option'),
            service('sylius.factory.product_option_value'),
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.product_review', ProductReviewContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.factory.product_review'),
            service('sylius.repository.product_review'),
            service('sylius_abstraction.state_machine'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.product_taxon', ProductTaxonContext::class)
        ->args([
            service('sylius.factory.product_taxon'),
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.promotion', PromotionContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.factory.promotion_action'),
            service('sylius.factory.promotion_coupon'),
            service('sylius.factory.promotion_rule'),
            service('sylius.repository.promotion'),
            service('sylius.generator.promotion_coupon'),
            service('doctrine.orm.entity_manager'),
            service('sylius.fixture.example_factory.promotion'),
            service('sylius.command_bus'),
            service('sylius.repository.order'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.admin_security', AdminSecurityContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.admin_security'),
            service('sylius.fixture.example_factory.admin_user'),
            service('sylius.repository.admin_user'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.admin_api_security', AdminSecurityContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.api_admin_security'),
            service('sylius.fixture.example_factory.admin_user'),
            service('sylius.repository.admin_user'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.shop_security', ShopSecurityContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.shop_security'),
            service('sylius.fixture.example_factory.shop_user'),
            service('sylius.repository.shop_user'),
            service('lexik_jwt_authentication.jwt_manager'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.shop_api_security', ShopSecurityContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.api_shop_security'),
            service('sylius.fixture.example_factory.shop_user'),
            service('sylius.repository.shop_user'),
            service('lexik_jwt_authentication.jwt_manager'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.shipping', ShippingContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.shipping_method'),
            service('sylius.repository.zone'),
            service('sylius.fixture.example_factory.shipping_method'),
            service('sylius.factory.shipping_method_rule'),
            service('sylius.manager.shipping_method'),
        ])
    ;

    $services
        ->set(
            'sylius.behat.context.setup.shipping_category',
            ShippingCategoryContext::class,
        )
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.factory.shipping_category'),
            service('sylius.repository.shipping_category'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.taxation', TaxationContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.factory.tax_rate'),
            service('sylius.factory.tax_category'),
            service('sylius.repository.tax_rate'),
            service('sylius.repository.tax_category'),
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.taxonomy', TaxonomyContext::class)
        ->args([
            service('sylius.repository.taxon'),
            service('sylius.factory.taxon'),
            service('sylius.factory.taxon_translation'),
            service('sylius.factory.taxon_image'),
            service('doctrine.orm.entity_manager'),
            service('sylius.uploader.image'),
            service('sylius.generator.taxon_slug'),
            service('behat.mink.parameters'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.theme', ThemeContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.theme'),
            service('sylius.manager.channel'),
            service(TestThemeConfigurationManagerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.user', UserContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.shop_user'),
            service('sylius.fixture.example_factory.shop_user'),
            service('sylius.manager.shop_user'),
            service('sylius.command_bus'),
            '%sylius.shop_user.token.password_reset.ttl%',
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.zone', ZoneContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.zone'),
            service('doctrine.orm.entity_manager'),
            service('sylius.factory.zone'),
            service('sylius.factory.zone_member'),
        ])
    ;

    $services
        ->set(CatalogPromotionContext::class)
        ->args([
            service('sylius.fixture.example_factory.catalog_promotion'),
            service('sylius.factory.catalog_promotion_scope'),
            service('sylius.factory.catalog_promotion_action'),
            service('sylius.manager.catalog_promotion'),
            service('sylius.repository.channel'),
            service('sylius_abstraction.state_machine'),
            service('sylius.event_bus'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.setup.payment_request', PaymentRequestContext::class)
        ->args([
            service('sylius.command_bus'),
            service('sylius.repository.payment_request'),
            service('sylius.factory.payment_request'),
            service('sylius_abstraction.state_machine'),
        ])
    ;
};
