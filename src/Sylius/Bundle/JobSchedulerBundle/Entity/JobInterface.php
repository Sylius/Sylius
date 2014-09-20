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
 * Job entity interface
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface JobInterface
{
    /**
     * Returns id
     *
     * @return mixed
     */
    public function getId();

    /**
     * Returns Job Schedule
     *
     * @return string
     */
    public function getSchedule();

    /**
     * Returns Job Environment
     *
     * @return mixed
     */
    public function getEnvironment();

    /**
     * Returns Job Server Type
     *
     * @return mixed
     */
    public function getServerType();

    /**
     * Returns Job Command
     *
     * @return mixed
     */
    public function getCommand();

    /**
     * Returns if job is currently running
     *
     * @return mixed
     */
    public function getIsRunning();

    /**
     * Sets if job is currently running
     *
     * @param $isRunning
     *
     * @return mixed
     */
    public function setIsRunning($isRunning);
} 