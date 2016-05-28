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
    const CACHE_KEY_PREFIX = 'rbac_role:';

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
     * @param Cache $cache
     * @param int $ttl
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
        if ($this->cache->contains($this->getCacheKey($role))) {
            return in_array($permissionCode, $this->cache->fetch($this->getCacheKey($role)));
        }

        $permissions = $this->map->getPermissions($role);
        $permissionsCache = [];

        foreach ($permissions as $permission) {
            $permissionsCache[] = $permission->getCode();
        }

        $this->cache->save($this->getCacheKey($role), $permissionsCache, $this->ttl);

        return in_array($permissionCode, $permissionsCache);
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
        return self::CACHE_KEY_PREFIX . $role->getCode();
    }
}
