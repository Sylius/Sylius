<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Security\Role\Provider;

use Doctrine\Common\Cache\Cache;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class CachedHierarchyProvider implements HierarchyProviderInterface
{
    const TTL = 60;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var HierarchyProviderInterface
     */
    protected $provider;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * Constructor.
     *
     * @param HierarchyProviderInterface $provider
     * @param Cache                      $cache
     * @param int                        $ttl
     */
    public function __construct(HierarchyProviderInterface $provider, Cache $cache, $ttl = self::TTL)
    {
        $this->provider = $provider;
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function getMap()
    {
        if ($this->cache->contains($this->getCacheKey())) {
            return $this->cache->fetch($this->getCacheKey());
        }

        $map = $this->provider->getMap();
        $this->cache->save($this->getCacheKey(), $map, $this->ttl);

        return $map;
    }

    /**
     * @return string
     */
    private function getCacheKey()
    {
        return 'role_hierarchy';
    }
}
