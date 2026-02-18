<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_managing_users'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.user',
                'sylius.behat.context.ui.admin.managing_customers',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.customer',
                'sylius.behat.context.ui.save',
                'sylius.behat.context.ui.shop.login',
                'sylius.behat.context.ui.user'
            )
            ->withFilter(new TagFilter('@managing_users&&@ui'))));
