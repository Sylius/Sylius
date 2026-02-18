<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Sylius\Behat\Context\Ui\Admin\ProductCreationContext;
use Sylius\Behat\Context\Ui\Admin\RemovingProductContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_panel'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.transform.admin',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.taxon',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.taxonomy',
                'sylius.behat.context.ui.admin.browsing_product_variants',
                'sylius.behat.context.ui.admin.managing_products',
                'sylius.behat.context.ui.save',
                'sylius.behat.context.ui.shop.browsing_product',
                ProductCreationContext::class,
                RemovingProductContext::class
            )
            ->withFilter(new TagFilter('@admin_panel&&@ui'))));
