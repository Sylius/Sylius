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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * Permission map interface.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PermissionMapInterface
{
    /**
     * @param RoleInterface $role
     * @param string        $permissionCode
     *
     * @return bool
     */
    public function hasPermission(RoleInterface $role, $permissionCode);

    /**
     * @param RoleInterface $role
     *
     * @return Collection
     */
    public function getPermissions(RoleInterface $role);
}
