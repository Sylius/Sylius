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
 * Validator interface
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface ValidatorInterface
{

    /**
     * Returns whether the  should be run or not
     *
     * @param $var
     *
     * @internal param $environment
     * @return boolean
     */
    public function isValid($var);

} 