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

/**
 * This service is responsible for deciding if current identity has the permission to do X.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
interface AuthorizationCheckerInterface
{
    /**
     * @param string $permissionCode
     *
     * @return bool
     */
    public function isGranted($permissionCode);
}
