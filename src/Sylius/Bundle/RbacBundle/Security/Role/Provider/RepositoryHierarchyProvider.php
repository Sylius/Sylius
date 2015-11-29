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

use Sylius\Bundle\RbacBundle\Security\Role\InflectorInterface;
use Sylius\Component\Rbac\Repository\RoleRepositoryInterface;
use Sylius\Component\Rbac\Repository\PermissionRepositoryInterface;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class RepositoryHierarchyProvider implements HierarchyProviderInterface
{
    /**
     * @var RoleRepositoryInterface
     */
    protected $roleRepository;

    /**
     * @var PermissionRepositoryInterface
     */
    protected $permissionRepository;

    /**
     * @var InflectorInterface
     */
    protected $inflector;

    /**
     * @var array
     */
    protected $map;

    /**
     * Constructor.
     *
     * @param RoleRepositoryInterface       $roleRepository
     * @param PermissionRepositoryInterface $permissionRepository
     * @param InflectorInterface            $inflector
     */
    public function __construct(
        RoleRepositoryInterface $roleRepository,
        PermissionRepositoryInterface $permissionRepository,
        InflectorInterface $inflector
    ) {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->inflector = $inflector;
    }

    /**
     * {@inheritdoc}
     */
    public function getMap()
    {
        if ($this->map) {
            return $this->map;
        }

        $this->map = array();

        if (!$roles = $this->roleRepository->findAll()) {
            throw new \RuntimeException('RBAC not initialized.');
        }

        $permissions = $this->permissionRepository->findAll();

        foreach ($this->sort($permissions) as $permission) {
            $this->addPermission($permission);
        }

        foreach ($this->sort($roles) as $role) {
            $this->addRole($role);
        }

        return $this->map;
    }

    /**
     * Add permission to map.
     *
     * @param PermissionInterface $permission
     */
    private function addPermission(PermissionInterface $permission)
    {
        $this->map[$permission->getCode()] = array();

        foreach ($this->permissionRepository->getChildPermissions($permission) as $childPermission) {
            $this->map[$permission->getCode()][] = $childPermission->getCode();
        }
    }

    /**
     * Add role to map.
     *
     * @param RoleInterface $role
     */
    private function addRole(RoleInterface $role)
    {
        $name = $this->inflector->toSecurityRole($role->getCode());

        if (isset($this->map[$name])) {
            return;
        }

        $this->map[$name] = array();
        $permissions = $role->getPermissions();

        foreach ($this->roleRepository->getChildRoles($role) as $childRole) {
            $this->map[$name][] = $this->inflector->toSecurityRole($childRole);
            foreach ($childRole->getPermissions() as $permission) {
                if (!$permissions->contains($permission)) {
                    $permissions->add($permission);
                }
            }
        }

        foreach ($permissions as $permission) {
            $this->map[$name][] = $permission->getCode();
            $this->map[$name] = array_merge($this->map[$permission->getCode()], $this->map[$name]);
        }
    }

    /**
     * @param array $rolesOrPermissions
     *
     * @return array
     */
    private function sort(array $rolesOrPermissions)
    {
        /**
         * @var $first  RoleInterface|PermissionInterface
         * @var $second RoleInterface|PermissionInterface
         */
        usort($rolesOrPermissions, function ($first, $second) {
            return ($first->getLeft() > $second->getLeft()) ? -1 : 1;
        });

        return $rolesOrPermissions;
    }
}
