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
    public function __construct(PermissionMapInterface $map, Cache $cache, $ttl = null)
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
            $permissionsCache = $this->cache->fetch($this->getCacheKey($role));

            return isset($permissionsCache[$permissionCode]);
        }

        $permissionsCache = array();

        foreach ($this->map->getPermissions($role) as $permission) {
            $permissionsCache[$permission->getCode()] = $permission->getCode();
        }

        $this->cache->save($this->getCacheKey($role), $permissionsCache, $this->ttl);

        return isset($permissionsCache[$permissionCode]);
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
