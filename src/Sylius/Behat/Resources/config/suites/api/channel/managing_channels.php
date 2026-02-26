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
use Sylius\Behat\Context\Api\Admin\ManagingChannelPriceHistoryConfigContext;
use Sylius\Behat\Context\Api\Admin\ManagingChannelsBillingDataContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_managing_channels', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.currency',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.taxonomy',
                'sylius.behat.context.setup.zone',
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.currency',
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.taxon',
                'sylius.behat.context.transform.zone',
            )
            ->withContexts(
                'sylius.behat.context.api.admin.managing_channels',
                'sylius.behat.context.api.admin.response',
                'sylius.behat.context.api.admin.save',
                'sylius.behat.context.api.debug',
                ManagingChannelPriceHistoryConfigContext::class,
                ManagingChannelsBillingDataContext::class,
            )
            ->withFilter(new TagFilter('@managing_channels&&@api'))
        )
    )
;
