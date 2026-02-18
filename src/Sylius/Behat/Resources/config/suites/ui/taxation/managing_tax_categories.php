<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_managing_tax_categories'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.tax_category',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.taxation',
                'sylius.behat.context.ui.admin.managing_tax_categories',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.admin.search_filter',
                'sylius.behat.context.ui.save'
            )
            ->withFilter(new TagFilter('@managing_tax_categories&&@ui'))));
