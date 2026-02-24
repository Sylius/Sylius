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
            (new Suite('ui_administrator_security'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.user',
                'sylius.behat.context.transform.admin',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.user',
                'sylius.behat.context.ui.admin.dashboard',
                'sylius.behat.context.ui.admin.login',
                'sylius.behat.context.ui.admin.managing_administrators',
                'sylius.behat.context.ui.email',
                'sylius.behat.context.ui.save',
            )
            ->withFilter(new TagFilter('@administrator_security&&@ui')),
        ),
    )
;
