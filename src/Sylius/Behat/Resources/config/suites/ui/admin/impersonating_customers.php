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
            (new Suite('ui_impersonating_customers'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.shop_security',
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
            )
            ->withContexts(
                'sylius.behat.context.ui.admin.impersonating_customers',
                'sylius.behat.context.ui.admin.managing_customers',
                'sylius.behat.context.ui.save',
                'sylius.behat.context.ui.shop.cart',
                'sylius.behat.context.ui.shop.checkout',
                'sylius.behat.context.ui.shop.checkout.complete',
                'sylius.behat.context.ui.shop.login',
            )
            ->withFilter(new TagFilter('@impersonating_customers&&@ui')),
        ),
    )
;
