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
use Sylius\Behat\Context\Api\Shop\AddressContext;
use Sylius\Behat\Context\Api\Shop\CartContext;
use Sylius\Behat\Context\Api\Shop\ChannelContext;
use Sylius\Behat\Context\Api\Shop\Checkout\CheckoutCompleteContext;
use Sylius\Behat\Context\Api\Shop\Checkout\CheckoutOrderDetailsContext;
use Sylius\Behat\Context\Api\Shop\Checkout\CheckoutShippingContext;
use Sylius\Behat\Context\Api\Shop\CheckoutContext;
use Sylius\Behat\Context\Api\Shop\ContactContext;
use Sylius\Behat\Context\Api\Shop\CurrencyContext;
use Sylius\Behat\Context\Api\Shop\CustomerContext;
use Sylius\Behat\Context\Api\Shop\ExchangeRateContext;
use Sylius\Behat\Context\Api\Shop\HomepageContext;
use Sylius\Behat\Context\Api\Shop\LocaleContext;
use Sylius\Behat\Context\Api\Shop\LoginContext;
use Sylius\Behat\Context\Api\Shop\OrderContext;
use Sylius\Behat\Context\Api\Shop\OrderItemContext;
use Sylius\Behat\Context\Api\Shop\PaymentContext;
use Sylius\Behat\Context\Api\Shop\PaymentRequestContext;
use Sylius\Behat\Context\Api\Shop\ProductAttributeContext;
use Sylius\Behat\Context\Api\Shop\ProductContext;
use Sylius\Behat\Context\Api\Shop\ProductReviewContext;
use Sylius\Behat\Context\Api\Shop\ProductVariantContext;
use Sylius\Behat\Context\Api\Shop\PromotionContext;
use Sylius\Behat\Context\Api\Shop\RegistrationContext;
use Sylius\Behat\Context\Api\Shop\ShipmentContext;
use Sylius\Behat\Context\Api\Shop\TaxonContext;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.behat.context.api.shop.address', AddressContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('api_platform.symfony.iri_converter'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.channel', ChannelContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            '%sylius.security.api_route%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.currency', CurrencyContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.cart', CartContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('sylius.resolver.product_variant'),
            service('api_platform.iri_converter'),
            service('sylius.behat.request_factory'),
            '%sylius.security.api_route%',
            service('sylius.repository.order'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.customer', CustomerContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service('sylius.behat.shared_storage'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.context.api.shop.registration'),
            service('sylius.behat.context.api.shop.login'),
            service('sylius.behat.context.setup.shop_api_security'),
            service('sylius.behat.request_factory'),
            '%sylius.security.api_route%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.exchange_rate', ExchangeRateContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.promotion', PromotionContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service('sylius.behat.shared_storage'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.checkout', CheckoutContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.context.api.shop.checkout.shipping'),
            service('sylius.repository.order'),
            service('sylius.repository.payment_method'),
            service('sylius.resolver.product_variant'),
            service('api_platform.iri_converter'),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.request_factory'),
            service('sylius.behat.factory.address'),
            '%sylius.model.shipping_method.class%',
            '%sylius.model.payment_method.class%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.homepage', HomepageContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('api_platform.iri_converter'),
            service('doctrine.orm.entity_manager'),
            '%sylius.security.api_route%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.login', LoginContext::class)
        ->args([
            service('sylius.behat.client.shop_api_platform_security_client'),
            service('sylius.behat.api_platform_client.shop'),
            service('api_platform.iri_converter'),
            service('test.client'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.request_factory'),
            '%sylius.security.api_route%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.product', ProductContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('api_platform.iri_converter'),
            service('sylius.behat.channel_context_setter'),
            service('sylius.behat.request_factory'),
            service('doctrine.orm.entity_manager'),
            '%sylius.security.api_route%',
            service('sylius.resolver.product_variant.default'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.product_attribute', ProductAttributeContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.product_variant', ProductVariantContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('api_platform.iri_converter'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.product_review', ProductReviewContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('api_platform.iri_converter'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.registration', RegistrationContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service('sylius.behat.context.api.shop.login'),
            service('sylius.behat.shared_storage'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.request_factory'),
            '%sylius.security.api_route%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.order', OrderContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service('sylius.behat.api_platform_client.admin'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('api_platform.iri_converter'),
            service('sylius.behat.api_admin_security'),
            service('sylius.behat.request_factory'),
            '%sylius.security.api_route%',
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.order_item', OrderItemContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.payment_request', PaymentRequestContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.request_factory'),
            service('sylius.repository.payment_request'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.payment', PaymentContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.shipment', ShipmentContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.locale', LocaleContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.contact', ContactContext::class)
        ->args([
            service('sylius.behat.request_factory'),
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.checkout.shipping', CheckoutShippingContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('sylius.behat.shared_storage'),
            service('api_platform.iri_converter'),
            service('sylius.repository.shipping_method'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.checkout.complete', CheckoutCompleteContext::class)
        ->args([
            service('sylius.behat.request_factory'),
            service('sylius.behat.shared_storage'),
            service('sylius.behat.api_platform_client.shop'),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.checkout.order_details', CheckoutOrderDetailsContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.api.shop.taxon', TaxonContext::class)
        ->args([
            service('sylius.behat.api_platform_client.shop'),
            service(ResponseCheckerInterface::class),
            service('api_platform.iri_converter'),
            service('doctrine.orm.entity_manager'),
        ])
    ;
};
