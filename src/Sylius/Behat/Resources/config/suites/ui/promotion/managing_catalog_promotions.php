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
use Sylius\Behat\Context\Setup\CatalogPromotionContext as SetupCatalogPromotionContext;
use Sylius\Behat\Context\Transform\CatalogPromotionContext as TransformCatalogPromotionContext;
use Sylius\Behat\Context\Ui\Admin\BrowsingCatalogPromotionProductVariantsContext;
use Sylius\Behat\Context\Ui\Admin\ManagingCatalogPromotionsContext;

return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('ui_managing_catalog_promotions'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.session',
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.product_taxon',
                'sylius.behat.context.setup.taxonomy',
                SetupCatalogPromotionContext::class,
            )
            ->withContexts(
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.taxon',
                TransformCatalogPromotionContext::class,
            )
            ->withContexts(
                'sylius.behat.context.ui.admin.notification',
                'sylius.behat.context.ui.admin.search_filter',
                'sylius.behat.context.ui.save',
                'sylius.behat.context.ui.shop.product',
                BrowsingCatalogPromotionProductVariantsContext::class,
                ManagingCatalogPromotionsContext::class,
            )
            ->withFilter(new TagFilter('@managing_catalog_promotions&&@ui')),
        ),
    )
;
