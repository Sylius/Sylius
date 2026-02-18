<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_customer_requesting_contact', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.mailer',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.shop_api_security',
                'sylius.behat.context.setup.user',
                'sylius.behat.context.api.debug',
                'sylius.behat.context.api.email',
                'sylius.behat.context.api.shop.contact'
            )
            ->withFilter(new TagFilter('@requesting_contact&&@api'))));
