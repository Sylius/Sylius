<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace Sylius\Bundle\ThemeBundle\Templating\Cache\Clearer;

use Doctrine\Common\Cache\Cache;
use Doctrine\Common\Cache\ClearableCache;
use Symfony\Component\HttpKernel\CacheClearer\CacheClearerInterface;

final class TemplatePathsCacheClearer implements CacheClearerInterface
{
    /**
     * @var Cache
     */
    private $cache;

    /**
     * @param Cache $cache
     */
    public function __construct(Cache $cache)
    {
        $this->cache = $cache;
    }

    /**
     * {@inheritdoc}
     */
    public function clear($cacheDir): void
    {
        if (!$this->cache instanceof ClearableCache) {
            return;
        }

        $this->cache->deleteAll();
    }
}
