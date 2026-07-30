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

return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('domain_managing_promotions'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.promotion',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.order',
            )
            ->withContexts(
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.promotion',
                'sylius.behat.context.transform.coupon',
                'sylius.behat.context.transform.shipping_method',
            )
            ->withContexts(
                'sylius.behat.context.domain.managing_promotions',
                'sylius.behat.context.domain.notification',
                'sylius.behat.context.domain.security',
            )
            ->withFilter(new TagFilter('@managing_promotions&&@domain')),
        ),
    )
;
