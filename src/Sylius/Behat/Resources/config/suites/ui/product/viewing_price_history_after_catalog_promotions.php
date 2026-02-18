<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Sylius\Behat\Context\Setup\CatalogPromotionContext;
use Sylius\Behat\Context\Ui\Admin\ChannelPricingLogEntryContext;
use Sylius\Behat\Context\Ui\Admin\ManagingCatalogPromotionsContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_viewing_price_history_after_catalog_promotions'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                CatalogPromotionContext::class,
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                CatalogPromotionContext::class,
                'sylius.behat.context.ui.admin.product_showpage',
                'sylius.behat.context.ui.save',
                ChannelPricingLogEntryContext::class,
                ManagingCatalogPromotionsContext::class
            )
            ->withFilter(new TagFilter('@viewing_price_history_after_catalog_promotions&&@ui'))));
