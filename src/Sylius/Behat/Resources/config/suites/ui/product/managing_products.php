<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

use Behat\Config\Config;
use Behat\Config\Filter\TagFilter;
use Behat\Config\Profile;
use Behat\Config\Suite;
use Sylius\Behat\Context\Ui\Admin\ManagingProductTaxonsContext;
use Sylius\Behat\Context\Ui\Admin\RemovingProductContext;

return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('ui_managing_products'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.cache',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.product_association',
                'sylius.behat.context.setup.product_attribute',
                'sylius.behat.context.setup.product_option',
                'sylius.behat.context.setup.product_review',
                'sylius.behat.context.setup.product_taxon',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.shipping_category',
                'sylius.behat.context.setup.taxonomy',
                'sylius.behat.context.setup.zone',
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.admin',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_association_type',
                'sylius.behat.context.transform.product_option',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.taxon',
                'sylius.behat.context.transform.zone',
            )
            ->withContexts(
                'sylius.behat.context.ui.admin.browsing_product_variants',
                'sylius.behat.context.ui.admin.managing_administrator_locale',
                'sylius.behat.context.ui.admin.managing_products',
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.admin.search_filter',
                'sylius.behat.context.ui.save',
                'sylius.behat.context.ui.shop.browsing_product',
                ManagingProductTaxonsContext::class,
                RemovingProductContext::class,
            )
            ->withFilter(new TagFilter('@managing_products&&@ui')),
        ),
    )
;
