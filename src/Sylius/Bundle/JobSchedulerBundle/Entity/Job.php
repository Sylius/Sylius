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
 * Job entity
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class Job implements JobInterface
{
    /**
     * @var
     */
    protected $id;

    /**
     * @var text
     */
    protected $command;

    /**
     * @var string
     */
    protected $description;

    /**
     * @var string
     */
    protected $schedule;

    /**
     * @var bool
     */
    protected $isRunning = false;

    /**
     * @var int
     */
    protected $priority = 0;

    /**
     * @var string
     */
    protected $environment;

    /**
     * @var string
     */
    protected $serverType;

    /**
     * @var bool
     */
    protected $active = true;

    /**
     * @var \Doctrine\Common\Collections\ArrayCollection
     */
    protected $logs;

    /**
     * created Time/Date
     *
     * @var \DateTime
     */
    protected $createdAt;

    /**
     * updated Time/Date
     *
     * @var \DateTime
     */
    protected $updatedAt;

    public function __construct()
    {
        $this->logs = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * Returns the las log of the job
     *
     * @return mixed|null
     */
    public function getLastLog()
    {
        if ($this->logs->count() > 0) {
            return $this->logs->last();
        } else {
            return null;
        }
    }

    /**
     * @param mixed $active
     */
    public function setActive($active)
    {
        $this->active = $active;
    }

    /**
     * @return mixed
     */
    public function getActive()
    {
        return $this->active;
    }

    /**
     * @param mixed $command
     */
    public function setCommand($command)
    {
        $this->command = $command;
    }

    /**
     * @return mixed
     */
    public function getCommand()
    {
        return $this->command;
    }

    /**
     * @param mixed $description
     */
    public function setDescription($description)
    {
        $this->description = $description;
    }

    /**
     * @return mixed
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param mixed $environment
     */
    public function setEnvironment($environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return mixed
     */
    public function getEnvironment()
    {
        return $this->environment;
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
     * @param mixed $isRunning
     */
    public function setIsRunning($isRunning)
    {
        $this->isRunning = $isRunning;
    }

    /**
     * @return mixed
     */
    public function getIsRunning()
    {
        return $this->isRunning;
    }

    /**
     * @param mixed $priority
     */
    public function setPriority($priority)
    {
        $this->priority = $priority;
    }

    /**
     * @return mixed
     */
    public function getPriority()
    {
        return $this->priority;
    }

    /**
     * @param mixed $schedule
     */
    public function setSchedule($schedule)
    {
        $this->schedule = $schedule;
    }

    /**
     * @return mixed
     */
    public function getSchedule()
    {
        return $this->schedule;
    }


    /**
     * @param mixed $serverType
     */
    public function setServerType($serverType)
    {
        $this->serverType = $serverType;
    }

    /**
     * @return mixed
     */
    public function getServerType()
    {
        return $this->serverType;
    }

    /**
     * @param $log
     */
    public function addLog($log)
    {
        $this->logs[] = $log;
    }

    /**
     * @return mixed
     */
    public function getLogs()
    {
        return $this->logs;
    }

    /**
     * Set createdAt
     *
     */
    public function setCreatedAt()
    {
        $this->createdAt = new \DateTime();
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set updatedAt
     */
    public function setUpdatedAt()
    {
        $this->updatedAt = new \DateTime();
    }

    /**
     * Get updatedAt
     *
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * Sets values from array
     *
     * @param $data
     */
    public function fromArray($data)
    {
        foreach ($data as $key => $value) {
            $method = 'set' . ucfirst($key);
            $this->$method($value);
        }
    }
} 