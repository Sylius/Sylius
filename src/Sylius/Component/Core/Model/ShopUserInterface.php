<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Core\Model;

use Sylius\Component\Customer\Model\CustomerAwareInterface;
use Sylius\Component\Rbac\Model\IdentityInterface;
use Sylius\Component\Rbac\Model\RoleInterface;
use Sylius\Component\User\Model\UserInterface as BaseUserInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface ShopUserInterface extends BaseUserInterface, IdentityInterface, CustomerAwareInterface
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
