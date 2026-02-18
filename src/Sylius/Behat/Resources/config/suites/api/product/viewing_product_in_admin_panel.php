<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Sylius\Behat\Context\Api\Admin\BrowsingCatalogPromotionProductVariantsContext;
use Sylius\Behat\Context\Setup\CatalogPromotionContext;
use Sylius\Behat\Context\Setup\PriceHistoryContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_viewing_product_in_admin_panel', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_association_type',
                'sylius.behat.context.transform.product_attribute',
                'sylius.behat.context.transform.product_option',
                'sylius.behat.context.transform.product_option_value',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_category',
                'sylius.behat.context.transform.tax_category',
                'sylius.behat.context.transform.taxon',
                CatalogPromotionContext::class,
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.calendar',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.product_association',
                'sylius.behat.context.setup.product_attribute',
                'sylius.behat.context.setup.product_option',
                'sylius.behat.context.setup.product_taxon',
                'sylius.behat.context.setup.shipping_category',
                'sylius.behat.context.setup.taxation',
                'sylius.behat.context.setup.taxonomy',
                CatalogPromotionContext::class,
                PriceHistoryContext::class,
                'sylius.behat.context.api.admin.managing_products',
                'sylius.behat.context.api.debug',
                BrowsingCatalogPromotionProductVariantsContext::class
            )
            ->withFilter(new TagFilter('@viewing_product_in_admin_panel&&@api'))));
