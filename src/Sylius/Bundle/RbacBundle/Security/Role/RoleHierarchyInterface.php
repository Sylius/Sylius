<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\RbacBundle\Security\Role;

use Symfony\Component\Security\Core\Role\RoleHierarchyInterface as BaseRoleHierarchyInterface;

/**
 * @author Christian Daguerre <christian@daguer.re>
 */
interface RoleHierarchyInterface extends BaseRoleHierarchyInterface
{
    /**
     * Check whether the given attribute (role or permission) exists
     * in the map.
     *
     * @param string $attribute Role or permission code.
     *
     * @return bool
     */
    public function attributeExists($attribute);
}
