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
    /**
     * @var RoleRepositoryInterface
     */
    private $roleRepository;

    /**
     * @var array<RoleInterface[]>
     */
    private $cache = array();

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

        return $this->cache[$identityHash] = $this->getIdentityRoles($identity);
    }

    /**
     * @param IdentityInterface $identity
     *
     * @return Collection|RoleInterface[]
     */
    private function getIdentityRoles(IdentityInterface $identity)
    {
        $roles = new ArrayCollection();

        foreach ($identity->getAuthorizationRoles() as $role) {
            $roles->add($role);

            foreach ($this->getChildRoles($role) as $childRole) {
                if (!$roles->contains($childRole)) {
                    $roles->add($childRole);
                }
            }
        }

        return $roles;
    }

    /**
     * @param RoleInterface $role
     *
     * @return RoleInterface[]
     */
    private function getChildRoles(RoleInterface $role)
    {
        return $this->roleRepository->getChildRoles($role);
    }
}
