<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_managing_countries'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.province',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.zone_member',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.zone',
                'sylius.behat.context.ui.admin.managing_countries',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.save'
            )
            ->withFilter(new TagFilter('@managing_countries&&@ui'))));
