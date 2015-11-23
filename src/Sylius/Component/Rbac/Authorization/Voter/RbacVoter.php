<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Authorization\Voter;

use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Provider\PermissionProviderInterface;
use Sylius\Component\Rbac\Authorization\PermissionMapInterface;
use Sylius\Component\Rbac\Resolver\RolesResolverInterface;
use Sylius\Component\Rbac\Exception\PermissionNotFoundException;
use Sylius\Component\Rbac\Exception\CredentialsTooBroadException;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
class RbacVoter implements RbacVoterInterface
{
    /**
     * @var PermissionProviderInterface
     */
    protected $permissionProvider;

    /**
     * @var PermissionMapInterface
     */
    protected $permissionMap;

    /**
     * @var RolesResolverInterface
     */
    protected $rolesResolver;

    /**
     * Constructor.
     *
     * @param RepositoryInterface    $permissionProvider
     * @param PermissionMapInterface $permissionMap
     * @param RolesResolverInterface $rolesResolver
     */
    public function __construct(
        PermissionProviderInterface $permissionProvider,
        PermissionMapInterface $permissionMap,
        RolesResolverInterface $rolesResolver
    ) {
        $this->permissionProvider = $permissionProvider;
        $this->permissionMap = $permissionMap;
        $this->rolesResolver = $rolesResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted(IdentityInterface $identity, $permissionCode, $resource = null)
    {
        $roles = $this->rolesResolver->getRoles($identity);

        foreach ($roles as $role) {
            // If it's a role and no resource was provided, return true
            if ($role->getCode() === $permissionCode) {
                if ($resource) {
                    throw new CredentialsTooBroadException($resource, $permissionCode);
                }
                return true;
            }

            // No such permission, continue looking for a role with the given code.
            if (null === $permission = $this->getPermission($permissionCode)) {
                continue;
            }

            // Continue looking for permission in other roles
            if (!$this->permissionMap->hasPermission($role, $permissionCode)) {
                continue;
            }

            // User has permission, return true if no resource was provided
            if (!$resource) {
                return true;
            }

            // Fail if a resource was provided, but the role is not specific enough
            if ($permission->hasChildren()) {
                throw new CredentialsTooBroadException($resource, $permissionCode);
            }

            return $this->hasPermissionForResource($identity, $permissionCode, $resource);
        }

        return false;
    }

    /**
     * Checks whether the identity has the permission for a given resource.
     * By the time this is called, we already know the user has the permission.
     *
     * @param IdentityInterface $identity
     * @param string            $permissionCode
     * @param mixed             $resource
     *
     * @return bool
     */
    protected function hasPermissionForResource(IdentityInterface $identity, $permissionCode, $resource)
    {
        return true;
    }

    /**
     * Checks whether a permission with the given code exists.
     *
     * @param string $code
     *
     * @return PermissionInterface|null
     */
    protected function getPermission($code)
    {
        try {
            return $this->permissionProvider->getPermission($code);
        } catch (PermissionNotFoundException $e) {
            return null;
        }
    }
}
