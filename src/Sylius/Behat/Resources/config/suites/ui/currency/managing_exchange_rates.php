<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_managing_exchange_rates'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.exchange_rate',
                'sylius.behat.context.ui.admin.managing_exchange_rates',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.save'
            )
            ->withFilter(new TagFilter('@managing_exchange_rates&&@ui'))));
