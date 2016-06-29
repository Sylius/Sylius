<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Core\Model;

use Sylius\Rbac\Model\IdentityInterface;
use Sylius\Rbac\Model\RoleInterface;
use Sylius\User\Model\UserInterface as BaseUserInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface UserInterface extends BaseUserInterface, IdentityInterface
{
    /**
     * @param RoleInterface $role
     */
    public function addAuthorizationRole(RoleInterface $role);

    /**
     * @param RoleInterface $role
     */
    public function removeAuthorizationRole(RoleInterface $role);

    /**
     * @param RoleInterface $role
     *
     * @return bool
     */
    public function hasAuthorizationRole(RoleInterface $role);
}
