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
 * Exception should be thrown when role does not exist.
 *
 * @author Christian Daguerre <christian@daguer.re>
 */
class RoleNotFoundException extends \Exception
{
    public function __construct($role)
    {
        parent::__construct(sprintf('Role "%s" does not exist!', $role));
    }
}
