<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_managing_customer_groups', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.customer_group',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.customer_group',
                'sylius.behat.context.api.admin.managing_customer_groups',
                'sylius.behat.context.api.admin.response',
                'sylius.behat.context.api.admin.save',
                'sylius.behat.context.api.debug'
            )
            ->withFilter(new TagFilter('@managing_customer_groups&&@api'))));
