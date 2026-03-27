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

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_accessing_cart', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.guest_cart',
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.cart',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.checkout.address',
                'sylius.behat.context.setup.checkout.payment',
                'sylius.behat.context.setup.checkout.shipping',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.shop_api_security',
                'sylius.behat.context.setup.user',
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.cart',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_method',
            )
            ->withContexts(
                'sylius.behat.context.api.debug',
                'sylius.behat.context.api.shop.cart',
                'sylius.behat.context.api.shop.checkout',
                'sylius.behat.context.api.shop.checkout.shipping',
                'sylius.behat.context.api.shop.login',
            )
            ->withFilter(new TagFilter('@accessing_cart&&@api'))
        )
    )
;
