<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_viewing_exchange_rates', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.exchange_rate',
                'sylius.behat.context.api.debug',
                'sylius.behat.context.api.shop.channel',
                'sylius.behat.context.api.shop.currency',
                'sylius.behat.context.api.shop.exchange_rate'
            )
            ->withFilter(new TagFilter('@viewing_exchange_rates&&@api'))));
