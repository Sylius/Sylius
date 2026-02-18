<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Sylius\Behat\Context\Setup\CatalogPromotionContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_removing_catalog_promotions', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.calendar',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.taxon',
                CatalogPromotionContext::class,
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.calendar',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.product_taxon',
                'sylius.behat.context.setup.taxonomy',
                CatalogPromotionContext::class,
                'sylius.behat.context.api.admin.managing_catalog_promotions',
                'sylius.behat.context.api.debug',
                'sylius.behat.context.api.shop.product_variant'
            )
            ->withFilter(new TagFilter('@removing_catalog_promotions&&@api'))));
