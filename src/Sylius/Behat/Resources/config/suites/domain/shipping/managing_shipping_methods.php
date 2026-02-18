<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('domain_managing_shipping_methods'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.domain.managing_shipping_methods',
                'sylius.behat.context.domain.security'
            )
            ->withFilter(new TagFilter('@managing_shipping_methods&&@domain'))));
