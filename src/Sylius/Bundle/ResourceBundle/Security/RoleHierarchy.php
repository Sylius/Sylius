<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\ResourceBundle\Security;

use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleHierarchy as BaseRoleHierarchy;

class RoleHierarchy extends BaseRoleHierarchy
{
    public function __construct(array $hierarchy)
    {
        $this->map = $hierarchy;
    }

    /**
     * {@inheritdoc}
     */
    public function getReachableRoles(array $roles)
    {
        $reachableRoles = $roles;
        foreach ($roles as $role) {
            if (!isset($this->map[$role->getRole()])) {
                continue;
            }

            foreach ($this->map[$role->getRole()] as $r) {
                if (isset($this->map[$r])) {
                    foreach ($this->map[$r] as $r2) {
                        $reachableRoles[] = new Role($r2);
                    }
                }

                $reachableRoles[] = new Role($r);
            }
        }

        return $reachableRoles;
    }
}
