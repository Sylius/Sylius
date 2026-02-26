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
        ->withSuite((new Suite('cli_installer'))
            ->withContexts(
                'sylius.behat.context.hook.doctrine_orm',
            )
            ->withContexts(
                'sylius.behat.context.cli.installer',
            )
            ->withFilter(new TagFilter('@installer&&@cli'))
        )
    )
;
