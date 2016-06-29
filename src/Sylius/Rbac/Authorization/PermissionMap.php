<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Rbac\Authorization;

use Sylius\Rbac\Exception\PermissionNotFoundException;
use Sylius\Rbac\Model\PermissionInterface;
use Sylius\Rbac\Model\RoleInterface;
use Sylius\Rbac\Provider\PermissionProviderInterface;
use Sylius\Rbac\Resolver\PermissionsResolverInterface;

/**
 * Permission map.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PermissionMap implements PermissionMapInterface
{
    /**
     * @var PermissionProviderInterface
     */
    protected $permissionProvider;

    /**
     * @var PermissionsResolverInterface
     */
    protected $permissionsResolver;

    /**
     * Cache.
     *
     * @var array
     */
    private $permissions = [];

    /**
     * @param PermissionProviderInterface $permissionProvider
     * @param PermissionsResolverInterface $permissionsResolver
     */
    public function __construct(
        PermissionProviderInterface $permissionProvider,
        PermissionsResolverInterface $permissionsResolver
    ) {
        $this->permissionProvider = $permissionProvider;
        $this->permissionsResolver = $permissionsResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function hasPermission(RoleInterface $role, $permissionCode)
    {
        $permission = $this->getPermission($permissionCode);

        if (null === $permission) {
            return false;
        }

        return $this->getPermissions($role)->contains($permission);
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions(RoleInterface $role)
    {
        return $this->permissionsResolver->getPermissions($role);
    }

    /**
     * @param string $code
     *
     * @return null|PermissionInterface
     */
    private function getPermission($code)
    {
        if (isset($this->permissions[$code])) {
            return $this->permissions[$code];
        }

        try {
            return $this->permissionProvider->getPermission($code);
        } catch (PermissionNotFoundException $exception) {
            return null;
        }
    }
}
