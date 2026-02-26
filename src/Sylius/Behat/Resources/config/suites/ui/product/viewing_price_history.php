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
use Sylius\Behat\Context\Ui\Admin\ChannelPricingLogEntryContext;

return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('ui_viewing_price_history'))
            ->withContexts(
                'sylius.behat.context.hook.bad_gateway',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.setup.admin_security',
                'sylius.behat.context.setup.admin_user',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                SetupCatalogPromotionContext::class,
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                TransformCatalogPromotionContext::class,
                'sylius.behat.context.ui.admin.managing_product_variants',
                'sylius.behat.context.ui.save',
                ChannelPricingLogEntryContext::class,
            )
            ->withFilter(new TagFilter('@viewing_price_history&&@ui')),
        ),
    )
;
