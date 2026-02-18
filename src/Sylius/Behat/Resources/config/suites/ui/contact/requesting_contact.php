<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_customer_requesting_contact'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.mailer',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.user',
                'sylius.behat.context.ui.email',
                'sylius.behat.context.ui.shop.contact'
            )
            ->withFilter(new TagFilter('@requesting_contact&&@ui'))));
