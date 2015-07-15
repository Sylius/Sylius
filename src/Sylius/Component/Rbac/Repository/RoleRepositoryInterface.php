<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Repository;

use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * Role repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface RoleRepositoryInterface extends RepositoryInterface
{
    /**
     * Get child roles.
     *
     * @param RoleInterface
     *
     * @return array
     */
    public function getChildRoles(RoleInterface $role);
}
