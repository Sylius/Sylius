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
use Sylius\Behat\Context\Worker\WorkerModeContext;

/*
 * The suite that measures request isolation in worker mode.
 *
 * It lives in the `default` profile, like every other Sylius suite, and is
 * selected with `--suite worker`. A profile of its own is not an option: Behat
 * merges every profile with `default` - suites and extensions both - so a
 * `worker` profile would inherit all the Sylius suites. In CI that meant
 * running @address_book against the worker and ending in a 502.
 *
 * The context is not a container service: it builds itself and reads the
 * channel addresses from WORKER_CHANNEL_A / WORKER_CHANNEL_B / WORKER_BASE_URL.
 * It talks to the application over a socket, because state carried between
 * requests exists only in the long-lived process on the other side.
 */
return (new Config())
    ->withProfile(
        (new Profile('default'))
        ->withSuite(
            (new Suite('worker'))
            ->withContexts(WorkerModeContext::class)
            ->withPaths('features/worker')
            ->withFilter(new TagFilter('@worker')),
        ),
    )
;
