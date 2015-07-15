<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Resolver;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Repository\PermissionRepositoryInterface;

/**
 * Nested Set permissions resolver for optimization.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class NestedSetPermissionsResolver implements PermissionsResolverInterface
{
    private $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getPermissions(RoleInterface $role)
    {
        $permissions = new ArrayCollection();

        foreach ($role->getPermissions() as $permission) {
            $permissions->add($permission);

            foreach ($this->getChildPermissions($permission) as $childPermission) {
                if (!$permissions->contains($childPermission)) {
                    $permissions->add($childPermission);
                }
            }
        }

        return $permissions;
    }

    /**
     * @param PermissionInterface $permission
     *
     * @return Collection
     */
    private function getChildPermissions(PermissionInterface $permission)
    {
        return $this->permissionRepository->getChildPermissions($permission);
    }
}
