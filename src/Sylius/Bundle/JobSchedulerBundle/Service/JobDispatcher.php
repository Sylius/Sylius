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

use Sylius\Bundle\JobSchedulerBundle\Validator\SchedulerValidatorInterface;


/**
 * Dispatches jobs asynchronously
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class JobDispatcher
{
    /**
     * @var JobManagerInterface
     */
    private $jobManager;

    /**
     * @var \Sylius\Bundle\JobSchedulerBundle\Validator\SchedulerValidatorInterface
     */
    private $validator;

    /**
     * @param SchedulerValidatorInterface $validator
     * @param JobManagerInterface         $jobManager
     */
    public function __construct(SchedulerValidatorInterface $validator, JobManagerInterface $jobManager)
    {
        $this->validator  = $validator;
        $this->jobManager = $jobManager;
    }

    /**
     * Fetches jobs from repository and runs them asynchronously
     *
     */
    public function runActiveJobs()
    {
        if ($this->validator->isSchedulerEnabled()) {
            $jobs = $this->jobManager->findActiveJobs();
            foreach ($jobs as $job) {
                if ($this->validator->isScheduleValid($job->getSchedule())) {
                    $this->jobManager->runJobAsync($job->getId());
                }
            }
        }
    }
}