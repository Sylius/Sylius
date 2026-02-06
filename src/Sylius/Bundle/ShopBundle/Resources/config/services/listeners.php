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

use Sylius\Bundle\ShopBundle\EventListener\CartItemAddListener;
use Sylius\Bundle\ShopBundle\EventListener\CartItemRemoveListener;
use Sylius\Bundle\ShopBundle\EventListener\CustomerEmailUpdaterListener;
use Sylius\Bundle\ShopBundle\EventListener\OrderCompleteListener;
use Sylius\Bundle\ShopBundle\EventListener\OrderCustomerIpListener;
use Sylius\Bundle\ShopBundle\EventListener\OrderIntegrityChecker;
use Sylius\Bundle\ShopBundle\EventListener\OrderIntegrityCheckerInterface;
use Sylius\Bundle\ShopBundle\EventListener\OrderLocaleAssigner;
use Sylius\Bundle\ShopBundle\EventListener\SessionCartSubscriber;
use Sylius\Bundle\ShopBundle\EventListener\ShopCartBlamerListener;
use Sylius\Bundle\ShopBundle\EventListener\ShopCustomerAccountSubSectionCacheControlSubscriber;
use Sylius\Bundle\ShopBundle\EventListener\UserCartRecalculationListener;
use Sylius\Bundle\ShopBundle\EventListener\UserImpersonatedListener;
use Sylius\Bundle\ShopBundle\EventListener\UserRegistrationListener;

return static function (ContainerConfigurator $container) {
    $services = $container->services();

    $services->defaults()->public();

    $services
        ->set('sylius_shop.listener.shop_cart_blamer', ShopCartBlamerListener::class)
        ->args([
            service('sylius.context.cart'),
            service('sylius.section_resolver.uri_based'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.security.implicit_login', 'method' => 'onImplicitLogin'])
        ->tag('kernel.event_listener', ['event' => 'security.interactive_login', 'method' => 'onInteractiveLogin'])
    ;

    $services
        ->set('sylius_shop.listener.customer_email_updater', CustomerEmailUpdaterListener::class)
        ->args([
            service('sylius.shop_user.token_generator.email_verification'),
            service('sylius.context.channel'),
            service('event_dispatcher'),
            service('request_stack'),
            service('sylius.section_resolver.uri_based'),
            service('security.token_storage'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.customer.pre_update', 'method' => 'eraseVerification'])
        ->tag('kernel.event_listener', ['event' => 'sylius.customer.post_update', 'method' => 'sendVerificationEmail'])
    ;

    $services
        ->set('sylius_shop.event_subscriber.shop_customer_account_sub_section_cache_control', ShopCustomerAccountSubSectionCacheControlSubscriber::class)
        ->args([service('sylius.section_resolver.uri_based')])
        ->tag('kernel.event_subscriber', ['event' => 'kernel.response'])
    ;

    $services
        ->set('sylius_shop.listener.order_customer_ip', OrderCustomerIpListener::class)
        ->args([
            service('sylius.assigner.customer_id'),
            service('request_stack'),
        ])
        ->tag('kernel.event_listener', ['event' => 'workflow.sylius_order_checkout.completed.complete', 'priority' => 500])
    ;

    $services
        ->set('sylius_shop.listener.order_complete', OrderCompleteListener::class)
        ->args([service('sylius_shop.mailer.order_email_manager')])
        ->tag('kernel.event_listener', ['event' => 'sylius.order.post_complete', 'method' => 'sendConfirmationEmail'])
    ;

    $services
        ->set('sylius_shop.listener.user_registration', UserRegistrationListener::class)
        ->args([
            service('sylius.manager.shop_user'),
            service('sylius.shop_user.token_generator.email_verification'),
            service('event_dispatcher'),
            service('sylius.context.channel'),
            service('security.helper'),
            '%sylius_shop.firewall_context_name%',
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.customer.post_register', 'method' => 'handleUserVerification'])
    ;

    $services
        ->set('sylius_shop.listener.order_integrity_checker', OrderIntegrityChecker::class)
        ->args([
            service('router'),
            service('sylius.manager.order'),
            service('sylius.checker.order.promotions_integrity'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.order.pre_complete', 'method' => 'check'])
    ;
    $services->alias(OrderIntegrityCheckerInterface::class, 'sylius_shop.listener.order_integrity_checker');

    $services
        ->set('sylius_shop.listener.order_locale_assigner', OrderLocaleAssigner::class)
        ->args([service('sylius.context.locale')])
        ->tag('kernel.event_listener', ['event' => 'sylius.order.pre_complete', 'method' => 'assignLocale'])
    ;

    $services
        ->set('sylius_shop.event_subscriber.session_cart', SessionCartSubscriber::class)
        ->args([
            service('sylius.context.cart'),
            service('sylius_shop.storage.cart_session'),
        ])
        ->tag('kernel.event_subscriber')
    ;

    $services
        ->set('sylius_shop.listener.user_cart_recalculation', UserCartRecalculationListener::class)
        ->args([
            service('sylius.context.cart'),
            service('sylius.order_processing.order_processor'),
            service('sylius.section_resolver.uri_based'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.security.implicit_login', 'method' => 'recalculateCartWhileLogin'])
        ->tag('kernel.event_listener', ['event' => 'security.interactive_login', 'method' => 'recalculateCartWhileLogin'])
    ;

    $services
        ->set('sylius_shop.listener.user_impersonated', UserImpersonatedListener::class)
        ->args([
            service('sylius_shop.storage.cart_session'),
            service('sylius.context.channel'),
            service('sylius.repository.order'),
        ])
        ->tag('kernel.event_listener', ['event' => 'sylius.user.security.impersonate', 'method' => 'onUserImpersonated'])
    ;

    $services
        ->set('sylius_shop.listener.cart_item_add', CartItemAddListener::class)
        ->args([service('sylius.modifier.order')])
        ->tag('kernel.event_listener', ['event' => 'sylius.cart_item_add', 'method' => 'addToOrder'])
    ;

    $services
        ->set('sylius_shop.listener.cart_item_remove', CartItemRemoveListener::class)
        ->args([service('sylius.modifier.order')])
        ->tag('kernel.event_listener', ['event' => 'sylius.cart_item_remove', 'method' => 'removeFromOrder'])
    ;
};
