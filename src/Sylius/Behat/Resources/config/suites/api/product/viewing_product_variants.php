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
        ->withSuite((new Suite('api_viewing_product_variants', [
            'javascript' => false,
        ]))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.product_option',
                'sylius.behat.context.transform.product_option_value',
                'sylius.behat.context.transform.product_variant',
                'sylius.behat.context.transform.shared_storage',
                'sylius.behat.context.api.debug',
                'sylius.behat.context.api.shop.channel',
                'sylius.behat.context.api.shop.product',
                'sylius.behat.context.api.shop.product_variant',
            )
            ->withFilter(new TagFilter('@viewing_product_variants&&@api'))
        )
    )
;
