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
            (new Suite('ui_managing_shipments'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.calendar',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.mailer',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.calendar',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.zone',
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.order',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.zone',
                'sylius.behat.context.ui.admin.managing_shipments',
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.email',
                'sylius.behat.context.ui.shop.cart',
            )
            ->withFilter(new TagFilter('@managing_shipments&&@ui')),
        ),
    )
;
