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

use Sylius\Bundle\JobSchedulerBundle\Entity\JobInterface;


/**
 * Job validator interface
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface JobValidatorInterface
{

    /**
     * Validates if the job should run in this environment and server
     *
     * @param JobInterface $JobInterface
     *
     * @return boolean
     */
    public function isValid(JobInterface $JobInterface);
} 