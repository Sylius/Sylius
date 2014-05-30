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
 * Scheduler validator interface
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
interface SchedulerValidatorInterface
{
    /**
     * Returns if global  is enabled
     *
     * @return boolean
     */
    public function isSchedulerEnabled();

    /**
     * Given a pattern it returns if it should run now
     *
     * @param $pattern
     *
     * @return boolean
     */
    public function isScheduleValid($pattern);
} 