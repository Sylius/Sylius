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

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

/*
 * Request isolation in worker mode, on the ordinary Sylius contexts.
 *
 * What makes these suites measure isolation is not their contexts but WHERE
 * the application runs. The features carry a session tag:
 *
 *   @mink:workerphp   plain HTTP, no browser - for steps that assert on the
 *                     URL, because a browser stays on the URL of a 404 and
 *                     SymfonyPage::verifyUrl() only compares URLs
 *   @javascript       a real browser - needed wherever the shop drives a Live
 *                     component, e.g. adding to the cart
 *
 * Both talk over a socket to a long-lived worker. Under the default `symfony`
 * session KernelBrowser rebuilds the kernel before every request, so nothing
 * survives from one request to the next and a leak cannot be seen at all.
 *
 * One suite per feature on purpose: contexts collide. ProductContext and
 * CheckoutCompleteContext both define "I should be notified that this product
 * does not have sufficient stock", and Behat refuses to load both.
 *
 * hook.doctrine_orm is the normal full purge and it is correct here - every
 * scenario builds its channels and products in its own Background. It leaves
 * the worker process alone, which matters: that state is what is measured.
 */
return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('ui_frankenphp_theme_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.hook.test_theme',
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.theme',
            )
            ->withContexts(
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.theme',
            )
            ->withContexts(
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.theme',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:theme')),
        )
        ->withSuite(
            (new Suite('ui_frankenphp_channel_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.product',
            )
            ->withContexts(
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
            )
            ->withContexts(
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.shop.product',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:channel')),
        )
        ->withSuite(
            (new Suite('ui_frankenphp_cart_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.guest_cart',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.cart',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.user',
                'sylius.behat.context.setup.zone',
            )
            ->withContexts(
                'sylius.behat.context.transform.cart',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_category',
                'sylius.behat.context.transform.zone',
            )
            ->withContexts(
                'sylius.behat.context.ui.browser',
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.shop.cart',
                'sylius.behat.context.ui.shop.product',
                'sylius.behat.context.ui.user',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:cart')),
        )
        ->withSuite(
            (new Suite('ui_frankenphp_customer_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.user',
            )
            ->withContexts(
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.user',
            )
            ->withContexts(
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.shop.account',
                'sylius.behat.context.ui.shop.authorization',
                'sylius.behat.context.ui.user',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:customer')),
        )
        ->withSuite(
            (new Suite('ui_frankenphp_admin_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.user',
            )
            ->withContexts(
                'sylius.behat.context.transform.admin',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.user',
            )
            ->withContexts(
                'sylius.behat.context.ui.admin.dashboard',
                'sylius.behat.context.ui.admin.login',
                'sylius.behat.context.ui.admin.managing_administrators',
                'sylius.behat.context.ui.admin.managing_translatable_entities',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.save',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:admin')),
        )
        ->withSuite(
            (new Suite('ui_frankenphp_product_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.product',
            )
            ->withContexts(
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
            )
            ->withContexts(
                'sylius.behat.context.ui.admin.login',
                'sylius.behat.context.ui.admin.managing_product_variants',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.save',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:product')),
        )
        ->withSuite(
            (new Suite('ui_frankenphp_currency_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.exchange_rate',
                'sylius.behat.context.setup.product',
            )
            ->withContexts(
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
            )
            ->withContexts(
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.shop.currency',
                'sylius.behat.context.ui.shop.product',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:currency')),
        )
        ->withSuite(
            (new Suite('ui_frankenphp_locale_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.product',
            )
            ->withContexts(
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.shared_storage',
            )
            ->withContexts(
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.shop.locale',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:locale')),
        )
        ->withSuite(
            (new Suite('ui_frankenphp_checkout_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.guest_cart',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.cart',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.checkout',
                'sylius.behat.context.setup.checkout.address',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.user',
                'sylius.behat.context.setup.zone',
            )
            ->withContexts(
                'sylius.behat.context.transform.cart',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.zone',
            )
            ->withContexts(
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.shop.account',
                'sylius.behat.context.ui.shop.checkout.addressing',
                'sylius.behat.context.ui.shop.checkout.complete',
                'sylius.behat.context.ui.shop.checkout.payment',
                'sylius.behat.context.ui.shop.checkout.shipping',
                'sylius.behat.context.ui.shop.checkout.thank_you',
                'sylius.behat.context.ui.shop.homepage',
                'sylius.behat.context.ui.user',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:checkout')),
        )
        ->withSuite(
            (new Suite('ui_frankenphp_promotion_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.guest_cart',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.cart',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.promotion',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.user',
                'sylius.behat.context.setup.zone',
            )
            ->withContexts(
                'sylius.behat.context.transform.cart',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.promotion',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_category',
                'sylius.behat.context.transform.zone',
            )
            ->withContexts(
                'sylius.behat.context.ui.browser',
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.shop.cart',
                'sylius.behat.context.ui.shop.product',
                'sylius.behat.context.ui.user',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:promotion')),
        )
        ->withSuite(
            (new Suite('api_frankenphp_shipping_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.api.admin.managing_shipping_methods',
                'sylius.behat.context.api.admin.response',
                'sylius.behat.context.api.admin.save',
                'sylius.behat.context.api.admin.translation',
                'sylius.behat.context.api.debug',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.zone',
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_calculator',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.zone',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:shipping')),
        )
        ->withSuite(
            (new Suite('api_frankenphp_isolation'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.user',
            )
            ->withContexts(
                'sylius.behat.context.transform.cart',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.order',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.user',
            )
            ->withContexts(
                'sylius.behat.context.api.shop.channel',
                'sylius.behat.context.api.shop.product',
                'sylius.behat.context.api.shop.cart',
                'sylius.behat.context.api.shop.login',
                'sylius.behat.context.api.shop.customer',
            )
            ->withPaths('features/frankenphp/request_isolation')
            ->withFilter(new TagFilter('@worker&&@worker:api')),
        ),
    )
;
