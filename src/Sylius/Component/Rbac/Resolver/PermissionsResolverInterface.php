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

use Doctrine\Common\Collections\Collection;
use Sylius\Component\Rbac\Model\RoleInterface;

/**
 * Service implementing this service is responsible for getting all applicable permissions from Role.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PermissionsResolverInterface
{
    /**
     * Get all applicable permissions from role.
     *
     * @param RoleInterface
     *
     * @return Collection
     */
    public function getPermissions(RoleInterface $role);
}
