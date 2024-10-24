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

namespace Sylius\Component\Locale\Provider;

use Symfony\Contracts\Cache\CacheInterface;

final class CachedLocaleCollectionProvider implements LocaleCollectionProviderInterface
{
    public const LOCALES_CACHE_KEY = 'sylius_locales';

    public function __construct(private LocaleCollectionProviderInterface $decorated, private CacheInterface $cache)
    {
    }

    public function getAll(): array
    {
        return $this->cache->get(self::LOCALES_CACHE_KEY, function () {
            return $this->decorated->getAll();
        });
    }
}
