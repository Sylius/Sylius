<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Sylius\Behat\Context\Setup\CatalogPromotionContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_viewing_price_history_after_catalog_promotions', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                CatalogPromotionContext::class,
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                CatalogPromotionContext::class,
                'sylius.behat.context.api.admin.channel_pricing_log_entry',
                'sylius.behat.context.api.admin.managing_catalog_promotions',
                'sylius.behat.context.api.debug'
            )
            ->withFilter(new TagFilter('@viewing_price_history_after_catalog_promotions&&@api'))));
