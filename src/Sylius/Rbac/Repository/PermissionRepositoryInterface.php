<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Rbac\Repository;

use Sylius\Rbac\Model\PermissionInterface;
use Sylius\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PermissionRepositoryInterface extends RepositoryInterface
{
    /**
     * @param PermissionInterface
     *
     * @return array
     */
    public function getChildPermissions(PermissionInterface $permission);
}
