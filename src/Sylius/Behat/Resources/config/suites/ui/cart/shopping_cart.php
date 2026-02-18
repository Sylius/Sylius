<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Sylius\Behat\Context\Setup\CatalogPromotionContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_shopping_cart'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.guest_cart',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_option',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_category',
                'sylius.behat.context.transform.tax_category',
                'sylius.behat.context.transform.zone',
                'sylius.behat.context.setup.cart',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.checkout.address',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.exchange_rate',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.promotion',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.shipping_category',
                'sylius.behat.context.setup.shop_security',
                'sylius.behat.context.setup.taxation',
                'sylius.behat.context.setup.user',
                'sylius.behat.context.setup.zone',
                CatalogPromotionContext::class,
                'sylius.behat.context.ui.browser',
                'sylius.behat.context.ui.channel',
                'sylius.behat.context.ui.save',
                'sylius.behat.context.ui.shop.cart',
                'sylius.behat.context.ui.shop.currency',
                'sylius.behat.context.ui.shop.product',
                'sylius.behat.context.ui.shop.registration',
                'sylius.behat.context.ui.user'
            )
            ->withFilter(new TagFilter('@shopping_cart&&@ui'))));
