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

use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Resource\Repository\ResourceRepositoryInterface;

/**
 * Permission repository.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PermissionRepositoryInterface extends ResourceRepositoryInterface
{
    /**
     * Get child permissions.
     *
     * @param PermissionInterface
     *
     * @return array
     */
    public function getChildPermissions(PermissionInterface $permission);
}
