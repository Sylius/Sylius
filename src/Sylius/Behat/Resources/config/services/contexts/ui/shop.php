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

use Sylius\Behat\Context\Ui\Shop\AccountContext;
use Sylius\Behat\Context\Ui\Shop\AddressBookContext;
use Sylius\Behat\Context\Ui\Shop\AuthorizationContext;
use Sylius\Behat\Context\Ui\Shop\BrowsingProductContext;
use Sylius\Behat\Context\Ui\Shop\CartContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutAddressingContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutCompleteContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutOrderDetailsContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutPaymentContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutShippingContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\CheckoutThankYouContext;
use Sylius\Behat\Context\Ui\Shop\Checkout\RegistrationAfterCheckoutContext;
use Sylius\Behat\Context\Ui\Shop\CheckoutContext;
use Sylius\Behat\Context\Ui\Shop\ContactContext;
use Sylius\Behat\Context\Ui\Shop\CurrencyContext;
use Sylius\Behat\Context\Ui\Shop\ErrorPageContext;
use Sylius\Behat\Context\Ui\Shop\HomepageContext;
use Sylius\Behat\Context\Ui\Shop\LocaleContext;
use Sylius\Behat\Context\Ui\Shop\LoginContext;
use Sylius\Behat\Context\Ui\Shop\PaymentRequestContext;
use Sylius\Behat\Context\Ui\Shop\ProductAttributeContext;
use Sylius\Behat\Context\Ui\Shop\ProductContext;
use Sylius\Behat\Context\Ui\Shop\ProductReviewContext;
use Sylius\Behat\Context\Ui\Shop\RegistrationContext;
use Sylius\Behat\Element\Product\ShowPage\LowestPriceInformationElementInterface;
use Sylius\Behat\Element\Shop\CartWidgetElementInterface;
use Sylius\Behat\Element\Shop\CheckoutSubtotalElementInterface;
use Sylius\Behat\Service\SessionManagerInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius.behat.context.ui.shop.checkout', CheckoutContext::class)
        ->args([
            service('sylius.behat.page.shop.checkout.address'),
            service('sylius.behat.page.shop.checkout.select_payment'),
            service('sylius.behat.page.shop.checkout.select_shipping'),
            service('sylius.behat.page.shop.checkout.complete'),
            service('sylius.behat.page.shop.account.register'),
            service('sylius.behat.element.shop.account.register'),
            service('sylius.behat.current_page_resolver'),
            service('sylius.behat.context.ui.shop.checkout.addressing'),
            service('sylius.behat.context.ui.shop.checkout.shipping'),
            service('sylius.behat.context.ui.shop.checkout.payment'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.checkout.thank_you', CheckoutThankYouContext::class)
        ->args([
            service('sylius.behat.page.shop.order.thank_you'),
            service('sylius.behat.page.shop.account.order.show'),
            service('sylius.repository.order'),
            service('sylius.behat.page.shop.order.show'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.checkout.order_details', CheckoutOrderDetailsContext::class)
        ->args([
            service('sylius.behat.page.shop.order.show'),
            service('sylius.behat.page.shop.order.thank_you'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.checkout.addressing', CheckoutAddressingContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.shop.checkout.address'),
            service('sylius.behat.factory.address'),
            service('sylius.comparator.address'),
            service('sylius.behat.page.shop.checkout.select_shipping'),
            service('sylius.behat.java_script_test_helper'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.checkout.shipping', CheckoutShippingContext::class)
        ->args([
            service('sylius.behat.page.shop.checkout.select_shipping'),
            service('sylius.behat.page.shop.checkout.select_payment'),
            service('sylius.behat.page.shop.checkout.complete'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.checkout.payment', CheckoutPaymentContext::class)
        ->args([
            service('sylius.behat.page.shop.checkout.select_payment'),
            service('sylius.behat.page.shop.checkout.complete'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.checkout.complete', CheckoutCompleteContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.shop.checkout.complete'),
            service('sylius.behat.notification_checker.shop'),
            service('sylius.behat.page.shop.order.thank_you'),
            service('sylius.repository.order'),
            service('doctrine.orm.entity_manager'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.checkout.registration_after_checkout', RegistrationAfterCheckoutContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.shop.account.login'),
            service('sylius.behat.page.shop.order.thank_you'),
            service('sylius.behat.page.shop.home'),
            service('sylius.behat.page.shop.account.verify'),
            service('sylius.behat.page.shop.account.register.thank_you'),
            service('sylius.behat.page.shop.account.dashboard'),
            service('sylius.behat.element.shop.account.register'),
            service('sylius.repository.customer'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.account', AccountContext::class)
        ->args([
            service('sylius.behat.page.shop.account.dashboard'),
            service('sylius.behat.page.shop.account.profile_update'),
            service('sylius.behat.page.shop.account.change_password'),
            service('sylius.behat.page.shop.account.order.index'),
            service('sylius.behat.page.shop.account.order.show'),
            service('sylius.behat.page.shop.account.login'),
            service('sylius.behat.notification_checker.shop'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.address_book', AddressBookContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.repository.address'),
            service('sylius.behat.page.shop.account.address_book.index'),
            service('sylius.behat.page.shop.account.address_book.create'),
            service('sylius.behat.page.shop.account.address_book.update'),
            service('sylius.behat.current_page_resolver'),
            service('sylius.behat.notification_checker.shop'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.authorization', AuthorizationContext::class)
        ->args([
            service('sylius.behat.page.shop.account.login'),
            service('sylius.behat.page.shop.account.register'),
            service('sylius.behat.element.shop.account.register'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.cart', CartContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.shop.cart_summary'),
            service('sylius.behat.page.shop.checkout.address'),
            service(CheckoutSubtotalElementInterface::class),
            service('sylius.behat.page.shop.product.show'),
            service(CartWidgetElementInterface::class),
            service('sylius.behat.notification_checker.shop'),
            service(SessionManagerInterface::class),
            service('sylius.behat.element.browser'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.contact', ContactContext::class)
        ->args([
            service('sylius.behat.page.shop.contact'),
            service('sylius.behat.notification_checker.shop'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.currency', CurrencyContext::class)
        ->args([service('sylius.behat.page.shop.home')])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.error_page', ErrorPageContext::class)
        ->args([service('sylius.behat.page.error')])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.homepage', HomepageContext::class)
        ->args([
            service('sylius.behat.page.shop.home'),
            service('sylius.behat.element.shop.menu'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.locale', LocaleContext::class)
        ->args([
            service('sylius.behat.page.shop.home'),
            service('sylius.behat.shared_storage'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.login', LoginContext::class)
        ->args([
            service('sylius.behat.page.shop.home'),
            service('sylius.behat.page.shop.account.login'),
            service('sylius.behat.page.shop.account.register'),
            service('sylius.behat.page.shop.account.request_password_reset'),
            service('sylius.behat.page.shop.account.reset_password'),
            service('sylius.behat.page.shop.account.well_known_password_change'),
            service('sylius.behat.element.shop.account.register'),
            service('sylius.behat.notification_checker.shop'),
            service('sylius.behat.current_page_resolver'),
            service('sylius.behat.shared_storage'),
            service('sylius.repository.customer'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.product', ProductContext::class)
        ->args([
            service('sylius.behat.page.shop.product.show'),
            service('sylius.behat.page.shop.product.index'),
            service('sylius.behat.page.shop.product_reviews.index'),
            service('sylius.behat.page.error'),
            service('sylius.behat.element.product.index.vertical_menu'),
            service('sylius.behat.channel_context_setter'),
            service(LowestPriceInformationElementInterface::class),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.product_attribute', ProductAttributeContext::class)
        ->args([service('sylius.behat.page.shop.product.show')])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.browsing_product', BrowsingProductContext::class)
        ->args([service('sylius.behat.page.shop.product.show')])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.product_review', ProductReviewContext::class)
        ->args([
            service('sylius.behat.page.shop.product_reviews.create'),
            service('sylius.behat.notification_checker.shop'),
            service('sylius.behat.page.shop.product_reviews.index'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.registration', RegistrationContext::class)
        ->args([
            service('sylius.behat.shared_storage'),
            service('sylius.behat.page.shop.account.dashboard'),
            service('sylius.behat.page.shop.home'),
            service('sylius.behat.page.shop.account.login'),
            service('sylius.behat.page.shop.account.register'),
            service('sylius.behat.page.shop.account.register.thank_you'),
            service('sylius.behat.page.shop.account.verify'),
            service('sylius.behat.page.shop.account.profile_update'),
            service('sylius.behat.element.shop.account.register'),
            service('sylius.behat.notification_checker.shop'),
            service('sylius.repository.customer'),
        ])
    ;

    $services
        ->set('sylius.behat.context.ui.shop.payment_request', PaymentRequestContext::class)
        ->args([
            service('sylius.repository.payment_request'),
            service('sylius.behat.page.shop.payment_request.payment_method_notify'),
            service('sylius.behat.page.shop.payment_request.payment_request_notify'),
            service('doctrine.orm.entity_manager'),
        ])
    ;
};
