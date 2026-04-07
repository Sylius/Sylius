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
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('api_managing_shipments', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.calendar',
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.hook.mailer',
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.calendar',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.geographical',
                'sylius.behat.context.setup.order',
                'sylius.behat.context.setup.payment',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.shipping',
                'sylius.behat.context.setup.zone',
            )
            ->withContexts(
                'sylius.behat.context.transform.address',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.country',
                'sylius.behat.context.transform.customer',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.order',
                'sylius.behat.context.transform.payment',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.transform.shipping_method',
                'sylius.behat.context.transform.zone',
            )
            ->withContexts(
                'sylius.behat.context.api.admin.managing_shipments',
                'sylius.behat.context.api.debug',
                'sylius.behat.context.api.email',
            )
            ->withFilter(new TagFilter('@managing_shipments&&@api'))
        )
    )
;
