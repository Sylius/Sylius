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
use Sylius\Behat\Context\Domain\ManagingPriceHistoryContext;
use Sylius\Behat\Context\Setup\PriceHistoryContext;

return (new Config())
    ->withProfile((new Profile('default'))
        ->withSuite((new Suite('domain_managing_price_history'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
            )
            ->withContexts(
                'sylius.behat.context.setup.channel',
                'sylius.behat.context.setup.product',
                'sylius.behat.context.setup.calendar',
                PriceHistoryContext::class,
            )
            ->withContexts(
                'sylius.behat.context.transform.channel',
                'sylius.behat.context.transform.lexical',
                'sylius.behat.context.transform.product',
                'sylius.behat.context.transform.shared_storage',
            )
            ->withContexts(
                ManagingPriceHistoryContext::class,
            )
            ->withFilter(new TagFilter('@managing_price_history&&@domain'))
        )
    )
;
