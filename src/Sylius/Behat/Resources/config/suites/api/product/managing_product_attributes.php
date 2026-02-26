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
        ->withSuite((new Suite('api_managing_product_attributes', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
            )
            ->withContexts(
                'sylius.behat.context.setup.admin_api_security',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.locale',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.product_attribute',
            )
            ->withContexts(
                'sylius.behat.context.transform.locale',
                'sylius.behat.context.transform.product_attribute',
                'sylius.behat.context.transform.shared_storage',
            )
            ->withContexts(
                'sylius.behat.context.api.admin.managing_product_attributes',
                'sylius.behat.context.api.admin.response',
                'sylius.behat.context.api.admin.save',
                'sylius.behat.context.api.admin.translation',
                'sylius.behat.context.api.debug',
            )
            ->withFilter(new TagFilter('@managing_product_attributes&&@api'))
        )
    )
;
