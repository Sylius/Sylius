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

use Sylius\Bundle\JobSchedulerBundle\Entity\JobInterface;
use Sylius\Bundle\JobSchedulerBundle\Entity\JobStatus;
use Sylius\Bundle\JobSchedulerBundle\Entity\JobLogInterface;


/**
 * Factory for job logs
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class JobLogFactory implements JobLogFactoryInterface
{

    /**
     * @var \Sylius\Bundle\JobSchedulerBundle\Entity\JobLogInterface
     */
    private $log;

    /**
     * @param JobLogInterface $log
     */
    public function __construct(JobLogInterface $log)
    {
        $this->log = $log;
    }

    /**
     * Creates a log
     *
     * @param JobInterface $Job
     *
     * @return JobLogInterface
     */
    public function createLog(JobInterface $job)
    {
        $this->log->setJob($job);
        $this->log->setStatus(JobStatus::SUCCESS);

        return $this->log;
    }
}