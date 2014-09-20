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
 * Job log entity interface
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface JobLogInterface
{

    /**
     * Logs an error
     *
     * @param $buffer
     */
    public function addError($buffer);

    /**
     * Logs output
     *
     * @param $buffer
     */
    public function addOutput($buffer);

    /**
     * Registers job start
     *
     */
    public function registerStart();

    /**
     * Registers job end
     *
     */
    public function registerEnd();
} 