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
 * Should be thrown when the application tries to check a meta permission
 * or role against a resource with a broader scope than a specific action.
 *
 * @author Christian Daguerre <christian@daguer.re>
 */
class CredentialsTooBroadException extends \Exception
{
    public function __construct($resource, $permissionOrRoleCode)
    {
        parent::__construct(sprintf(
            'Only permissions relating to a specific action can be tested against a specific resource, and they '
            . 'may not have child permissions. A "%s" resource was tested against the "%" role or permission.',
            get_class($resource),
            $permissionOrRoleCode
        ));
    }
}
