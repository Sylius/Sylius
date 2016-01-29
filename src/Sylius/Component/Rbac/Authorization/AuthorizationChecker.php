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

use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Provider\CurrentIdentityProviderInterface;
use Sylius\Component\Rbac\Resolver\RolesResolverInterface;

/**
 * Default authorization checker.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AuthorizationChecker implements AuthorizationCheckerInterface
{
    /**
     * @var CurrentIdentityProviderInterface
     */
    protected $currentIdentityProvider;

    /**
     * @var PermissionMapInterface
     */
    protected $permissionMap;

    /**
     * @var RolesResolverInterface
     */
    protected $rolesResolver;

    /**
     * @param CurrentIdentityProviderInterface $currentIdentityProvider
     * @param PermissionMapInterface $permissionMap
     * @param RolesResolverInterface $rolesResolver
     */
    public function __construct(
        CurrentIdentityProviderInterface $currentIdentityProvider,
        PermissionMapInterface $permissionMap,
        RolesResolverInterface $rolesResolver
    ) {
        $this->currentIdentityProvider = $currentIdentityProvider;
        $this->permissionMap = $permissionMap;
        $this->rolesResolver = $rolesResolver;
    }

    /**
     * {@inheritdoc}
     */
    public function isGranted($permissionCode)
    {
        $identity = $this->currentIdentityProvider->getIdentity();

        if (null === $identity) {
            return false;
        }

        if (!$identity instanceof IdentityInterface) {
            throw new \InvalidArgumentException('Current identity must implement "Sylius\Component\Rbac\Model\IdentityInterface".');
        }

        $roles = $this->rolesResolver->getRoles($identity);

        foreach ($roles as $role) {
            if ($this->permissionMap->hasPermission($role, $permissionCode)) {
                return true;
            }
        }

        return false;
    }
}
