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

namespace Sylius\Behat\Context\Hook;

use Behat\Behat\Context\Context;
use Psr\Cache\CacheItemPoolInterface;

final class MailerContext implements Context
{
    public function __construct(private CacheItemPoolInterface $cache)
    {
    }

    /**
     * @BeforeScenario @email
     */
    public function purgeMessages(): void
    {
        $this->cache->clear();
    }
}
