<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Service;

/**
 * Job manager interface
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface JobManagerInterface
{
    /**
     * Returns a collection of active jobs
     *
     * @return array
     */
    public function findActiveJobs();

    /**
     * Runs a job asynchronously
     *
     * @param $jobId
     */
    public function runJobAsync($jobId);
} 