<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_managing_channels'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.taxon',
                'sylius.behat.context.transform.zone',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.taxonomy',
                'sylius.behat.context.setup.zone',
                'sylius.behat.context.ui.admin.managing_channels',
                'sylius.behat.context.ui.admin.managing_channels_billing_data',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.save'
            )
            ->withFilter(new TagFilter('@managing_channels&&@ui'))));
