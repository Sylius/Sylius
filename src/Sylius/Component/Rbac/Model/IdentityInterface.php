<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Model;

/**
 * Identity.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface IdentityInterface
{
    /**
     * Get roles.
     *
     * @return RoleInterface[]
     */
    public function getAuthorizationRoles();
}
