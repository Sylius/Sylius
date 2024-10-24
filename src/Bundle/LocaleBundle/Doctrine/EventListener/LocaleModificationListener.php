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

namespace Sylius\Bundle\LocaleBundle\Doctrine\EventListener;

use Sylius\Component\Locale\Provider\CachedLocaleCollectionProvider;
use Symfony\Contracts\Cache\CacheInterface;

final class LocaleModificationListener
{
    public function __construct(private CacheInterface $cache)
    {
    }

    public function invalidateCachedLocales(): void
    {
        $this->cache->delete(CachedLocaleCollectionProvider::LOCALES_CACHE_KEY);
    }
}
