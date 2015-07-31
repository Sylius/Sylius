<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Component\ImportExport;

use Psr\Log\LoggerInterface;
use Sylius\Component\ImportExport\Model\JobInterface;
use Sylius\Component\ImportExport\Model\ProfileInterface;

/**
 * @author Łukasz Chruściel <lukasz.chruscie@lakion.com>
 */
interface JobRunnerInterface
{
    /**
     * Create import job.
     *
     * @param ProfileInterface $profile
     * @param LoggerInterface  $logger
     *
     * @return JobInterface
     */
    public function start(ProfileInterface $profile, LoggerInterface $logger);
    /**
     * Create job based on a given profile and starts it.
     *
     * @param ProfileInterface $profile
     * @param LoggerInterface  $logger
     * @param JobInterface     $job
     */
    public function run(ProfileInterface $profile, LoggerInterface $logger, JobInterface $job);
    /**
     * End import job.
     *
     * @param JobInterface    $job
     * @param LoggerInterface $logger
     * @param string          $jobStatus
     */
    public function end(JobInterface $job, LoggerInterface $logger, $jobStatus);
}
