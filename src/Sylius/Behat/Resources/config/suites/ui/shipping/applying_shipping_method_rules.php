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
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('ui_applying_shipping_method_rules'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.guest_cart',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.cart',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.checkout.address',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.promotion',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.taxation',
                'sylius.behat.context.setup.zone',
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.tax_category',
                'sylius.behat.context.transform.zone',
            )
            ->withContexts(
                'sylius.behat.context.ui.save',
                'sylius.behat.context.ui.shop.cart',
                'sylius.behat.context.ui.shop.checkout',
                'sylius.behat.context.ui.shop.checkout.addressing',
                'sylius.behat.context.ui.shop.checkout.payment',
                'sylius.behat.context.ui.shop.checkout.shipping',
            )
            ->withFilter(new TagFilter('@applying_shipping_method_rules&&@ui')),
        ),
    )
;
