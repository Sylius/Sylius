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
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * Default hierarchical permissions resolver.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PermissionsResolver implements PermissionsResolverInterface
{
    /**
     * {@inheritdoc}
     */
    public function getPermissions(RoleInterface $role)
    {
        $permissions = new ArrayCollection();

        $iterator = new \RecursiveIteratorIterator(
            new RecursivePermissionIterator($role->getPermissions()),
            \RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($iterator as $permission) {
            if (!$permissions->contains($permission)) {
                $permissions->add($permission);
            }
        }

        return $permissions;
    }
}
