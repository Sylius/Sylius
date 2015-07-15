<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\Rbac\Exception;

/**
 * Exception should be thrown when permission does not exist.
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class PermissionNotFoundException extends \Exception
{
    public function __construct($permission)
    {
        parent::__construct(sprintf('Permission "%s" does not exist!', $permission));
    }
}
