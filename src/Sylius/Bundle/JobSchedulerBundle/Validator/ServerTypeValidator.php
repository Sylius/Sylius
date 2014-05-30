<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Validator;

/**
 * Validates if app is running in the given server type
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class ServerTypeValidator implements ValidatorInterface
{
    /**
     * Returns if the server is valid and  job should run in it
     *
     * @param $serverType
     *
     * @return boolean
     */
    public function isValid($serverType = null)
    {
        if (is_null($serverType)) {
            return true;
        }

        return $serverType == getenv('ST');
    }
} 