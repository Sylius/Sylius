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

use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\Rbac\Model\PermissionInterface;
use Sylius\Component\Rbac\Exception\RoleNotFoundException;
use Sylius\Component\Rbac\Exception\PermissionNotFoundException;

/**
 * Allows retrieval and existence check of system roles and permissions by their code.
 *
 * @author Christian Daguerre <christian@daguer.re>
 */
interface CredentialProviderInterface
{
    /**
     * Whether the given role exists.
     *
     * @param string $code Role code
     *
     * @return bool
     */
    public function hasRole($code);

    /**
     * Get a role by code.
     *
     * @param string $code Role code
     *
     * @return RoleInterface
     *
     * @throws RoleNotFoundException If the role doesn't exist.
     */
    public function getRole($code);

    /**
     * Whether the given permission exists.
     *
     * @param string $code Permission code
     *
     * @return bool
     */
    public function hasPermission($code);

    /**
     * Get a permission by code.
     *
     * @param string $code Permission code
     *
     * @return PermissionInterface
     *
     * @throws PermissionNotFoundException If the permission doesn't exist.
     */
    public function getPermission($code);
}
