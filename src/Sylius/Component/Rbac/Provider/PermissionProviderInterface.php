<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Provider;

use Sylius\Component\Rbac\Exception\PermissionNotFoundException;
use Sylius\Component\Rbac\Model\PermissionInterface;

/**
 * Service implementing this interface should return an instance of currently used identity.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface PermissionProviderInterface
{
    /**
     * Get the permission instance by code.
     *
     * @param string $code
     *
     * @return PermissionInterface
     *
     * @throws PermissionNotFoundException When permission does not exist in the system
     */
    public function getPermission($code);
}
