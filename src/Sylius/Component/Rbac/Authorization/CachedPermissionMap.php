<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Authorization;

use Doctrine\Common\Cache\Cache;
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * Cached permission maps.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class CachedPermissionMap implements PermissionMapInterface
{
    const DEFAULT_TTL = 60;

    /**
     * @var PermissionMapInterface
     */
    protected $map;

    /**
     * @var Cache
     */
    protected $cache;

    /**
     * @var int
     */
    protected $ttl;

    /**
     * @param PermissionMapInterface $map
     * @param Cache                  $cache
     * @param int|null               $ttl
     */
    public function __construct(PermissionMapInterface $map, Cache $cache, $ttl = self::DEFAULT_TTL)
    {
        $this->map = $map;
        $this->cache = $cache;
        $this->ttl = $ttl;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPermission(RoleInterface $role, $permissionCode)
    {
        $permissionsCache = array();
        $cacheKey = $this->getCacheKey($role);
        if ($this->cache->contains($cacheKey)) {
            $permissionsCache = $this->cache->fetch($cacheKey);

            return isset($permissionsCache[$permissionCode]);
        }

        $found = false;
        foreach ($this->map->getPermissions($role) as $permission) {
            if ($permissionCode === $permission->getCode()) {
                $found = true;
            }

            $permissionsCache[$permission->getCode()] = true;
        }

        $this->cache->save($cacheKey, $permissionsCache, $this->ttl);

        return $found;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions(RoleInterface $role)
    {
        return $this->map->getPermissions($role);
    }

    /**
     * @param RoleInterface $role
     *
     * @return string
     */
    private function getCacheKey(RoleInterface $role)
    {
        return $role->getCode();
    }
}
