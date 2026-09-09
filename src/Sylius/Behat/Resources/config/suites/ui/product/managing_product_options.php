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
            (new Suite('ui_managing_product_options'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.product_option',
            )
            ->withContexts(
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_option',
                'sylius.behat.context.transform.product_option_value',
                'sylius.behat.context.transform.shared_storage',
            )
            ->withContexts(
                'sylius.behat.context.ui.admin.managing_product_options',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.admin.search_filter',
                'sylius.behat.context.ui.save',
            )
            ->withFilter(new TagFilter('@managing_product_options&&@ui')),
        ),
    )
;
