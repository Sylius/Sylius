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
            (new Suite('ui_managing_customers'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.cache',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.address',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.customer_group',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.user',
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.customer_group',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.user',
            )
            ->withContexts(
                'sylius.behat.context.ui.admin.dashboard',
                'sylius.behat.context.ui.admin.managing_customers',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.admin.search_filter',
                'sylius.behat.context.ui.save',
                'sylius.behat.context.ui.shop.login',
            )
            ->withFilter(new TagFilter('@managing_customers&&@ui')),
        ),
    )
;
