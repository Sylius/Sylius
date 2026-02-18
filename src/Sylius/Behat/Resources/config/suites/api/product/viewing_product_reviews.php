<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_viewing_product_reviews', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.product_review',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.api.debug',
                'sylius.behat.context.api.shop.product',
                'sylius.behat.context.api.shop.product_review'
            )
            ->withFilter(new TagFilter('@viewing_product_reviews&&@api'))));
