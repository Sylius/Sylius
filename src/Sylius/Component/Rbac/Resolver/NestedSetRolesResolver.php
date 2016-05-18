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
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Repository\RoleRepositoryInterface;

/**
 * Nested Set roles resolver for optimization.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class NestedSetRolesResolver implements RolesResolverInterface
{
    private $roleRepository;
    private $cache = [];

    public function __construct(RoleRepositoryInterface $roleRepository)
    {
        $this->roleRepository = $roleRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles(IdentityInterface $identity)
    {
        $identityHash = spl_object_hash($identity);

        if (isset($this->cache[$identityHash])) {
            return $this->cache[$identityHash];
        }

        $roles = new ArrayCollection();

        foreach ($identity->getAuthorizationRoles() as $role) {
            $childRoles = $this->getChildRoles($role);
            $roles->add($role);

            foreach ($childRoles as $childRole) {
                if (!$roles->contains($childRole)) {
                    $roles->add($childRole);
                }
            }
        }

        return $this->cache[$identityHash] = $roles;
    }

    /**
     * @param RoleInterface $role
     *
     * @return Collection
     */
    private function getChildRoles(RoleInterface $role)
    {
        return $this->roleRepository->getChildRoles($role);
    }
}
