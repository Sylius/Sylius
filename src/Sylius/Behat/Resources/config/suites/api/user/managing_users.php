<?php

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
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shop_user',
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.user',
                'sylius.behat.context.api.admin.managing_customers',
                'sylius.behat.context.api.admin.response',
                'sylius.behat.context.api.debug',
                'sylius.behat.context.api.shop.login'
            )
            ->withFilter(new TagFilter('@managing_users&&@api'))));
