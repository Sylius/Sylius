<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Sylius\Behat\Context\Domain\ManagingPriceHistoryContext;
use Sylius\Behat\Context\Setup\PriceHistoryContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('domain_managing_price_history'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                PriceHistoryContext::class,
                'sylius.behat.context.setup.calendar',
                ManagingPriceHistoryContext::class
            )
            ->withFilter(new TagFilter('@managing_price_history&&@domain'))));
