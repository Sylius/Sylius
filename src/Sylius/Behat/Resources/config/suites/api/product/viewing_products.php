<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Sylius\Behat\Context\Setup\CatalogPromotionContext;
use Sylius\Behat\Context\Setup\PriceHistoryContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_viewing_products', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_association_type',
                'sylius.behat.context.transform.product_option_value',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.taxon',
                CatalogPromotionContext::class,
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.setup.calendar',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.customer',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.product_association',
                'sylius.behat.context.setup.product_attribute',
                'sylius.behat.context.setup.product_review',
                'sylius.behat.context.setup.product_taxon',
                'sylius.behat.context.setup.shop_api_security',
                'sylius.behat.context.setup.taxonomy',
                CatalogPromotionContext::class,
                PriceHistoryContext::class,
                'sylius.behat.context.api.debug',
                'sylius.behat.context.api.shop.channel',
                'sylius.behat.context.api.shop.product',
                'sylius.behat.context.api.shop.product_attribute',
                'sylius.behat.context.api.shop.product_variant',
                'sylius.behat.context.api.shop.taxon'
            )
            ->withFilter(new TagFilter('@viewing_products&&@api'))));
