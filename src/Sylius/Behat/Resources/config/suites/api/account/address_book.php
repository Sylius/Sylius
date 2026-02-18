<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_address_book', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.guest_cart',
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.cart',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.province',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.user',
                'sylius.behat.context.setup.address',
                'sylius.behat.context.setup.cart',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.checkout',
                'sylius.behat.context.setup.checkout.address',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.shop_api_security',
                'sylius.behat.context.setup.user',
                'sylius.behat.context.api.debug',
                'sylius.behat.context.api.shop.address',
                'sylius.behat.context.api.shop.cart',
                'sylius.behat.context.api.shop.checkout',
                'sylius.behat.context.api.shop.save'
            )
            ->withFilter(new TagFilter('@address_book&&@api'))));
