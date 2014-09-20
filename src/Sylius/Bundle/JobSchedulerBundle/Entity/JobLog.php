<?php
/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Sylius\Bundle\JobSchedulerBundle\Entity;

/**
 * Job log entity
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class JobLog implements JobLogInterface
{
    /**
     * @var int
     */
    protected $id;

    /**
     * @var Sylius\Bundle\JobSchedulerBundle\Entity\Job
     */
    protected $job;


    /**
     * @var text
     */
    protected $output;

    /**
     * @var text
     */
    private $errors;

    /**
     * @var int
     */
    protected $status = JobStatus::RUNNING;

    /**
     * @var bigint
     */
    protected $startedAt;

    /**
     * @var string
     */
    protected $finishedAt;

    /**
     * @param mixed $job
     */
    public function setJob($job)
    {
        $this->job = $job;
        if ($this->job) {
            $this->job->addLog($this);
        }
    }

    /**
     * @return mixed
     */
    public function getJob()
    {
        return $this->job;
    }

    /**
     * @param mixed $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param mixed $output
     */
    public function setOutput($output)
    {
        $this->output = $output;
    }

    /**
     * @return mixed
     */
    public function getOutput()
    {
        return $this->output;
    }

    /**
     * @param mixed $errors
     */
    public function setErrors($errors)
    {
        $this->errors = $errors;
    }

    /**
     * @return mixed
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        if ($status == JobStatus::RUNNING) {
            return;
        }

        if ($this->status != JobStatus::FAILED) {
            $this->status = $status;
        }
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $finishedAt
     */
    public function setFinishedAt($finishedAt)
    {
        $this->finishedAt = $finishedAt;
    }

    /**
     * @return mixed
     */
    public function getFinishedAt()
    {
        return $this->finishedAt;
    }

    /**
     * @param $line
     */
    public function addError($line)
    {
        $this->setErrors($this->getErrors() . $line);
        $this->setStatus(JobStatus::FAILED);
    }

    /**
     * @param $line
     */
    public function addOutput($line)
    {
        $this->setOutput($this->getOutput() . $line);
    }

    /**
     * @param mixed $startedAt
     */
    public function setStartedAt($startedAt)
    {
        $this->startedAt = $startedAt;
    }

    /**
     * @return mixed
     */
    public function getStartedAt()
    {
        return $this->startedAt;
    }

    /**
     * Registers that the Job Started
     *
     */
    public function registerStart()
    {
        $this->setStartedAt(microtime(true));
    }

    /**
     * Registers that the Job Ended
     *
     */
    public function registerEnd()
    {
        $this->setFinishedAt(microtime(true));
    }
} 