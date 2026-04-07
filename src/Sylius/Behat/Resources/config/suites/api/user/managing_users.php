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
        ->withSuite((new Suite('api_managing_users', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.user',
            )
            ->withContexts(
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shop_user',
            )
            ->withContexts(
                'sylius.behat.context.api.admin.managing_customers',
                'sylius.behat.context.api.admin.response',
                'sylius.behat.context.api.debug',
                'sylius.behat.context.api.shop.login',
            )
            ->withFilter(new TagFilter('@managing_users&&@api'))
        )
    )
;
