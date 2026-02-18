<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('domain_shopping_cart'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.domain.cart'
            )
            ->withFilter(new TagFilter('@shopping_cart&&@domain'))));
