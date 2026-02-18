<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_adding_product_review'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_review',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.product_review',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.ui.shop.product_review'
            )
            ->withFilter(new TagFilter('@adding_product_review&&@ui'))));
