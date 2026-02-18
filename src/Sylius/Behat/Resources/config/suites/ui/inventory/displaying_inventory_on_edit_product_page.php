<?php

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('ui_inventory_on_product_page'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.ui.admin.browsing_product_variants',
                'sylius.behat.context.ui.admin.managing_inventory',
                'sylius.behat.context.ui.admin.managing_products',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.save'
            )
            ->withFilter(new TagFilter('@inventory_on_product_page&&@ui'))));
