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
 * Validates if app is running in the given environment
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class EnvironmentValidator implements ValidatorInterface
{
    /**
     * Returns if the environment is valid and  job should run in it
     *
     * @param $environment
     *
     * @return boolean
     */
    public function isValid($environment = null)
    {
        if (is_null($environment)) {
            return true;
        }

        return $environment == getenv('ENV');
    }
} 