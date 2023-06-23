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

namespace Sylius\Behat\Service\Provider;

use Psr\Cache\CacheItemPoolInterface;
use Sylius\Behat\Service\MessageSendCacher;

final class EmailMessagesProvider implements EmailMessagesProviderInterface
{
    public function __construct(private CacheItemPoolInterface $cache)
    {
    }

    public function provide(): array
    {
        return $this->cache->hasItem(MessageSendCacher::CACHE_KEY) ? $this->cache->getItem(MessageSendCacher::CACHE_KEY)->get() : [];
    }
}
