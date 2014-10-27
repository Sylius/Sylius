<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Component\Subscription\Scheduler;

use Sylius\Component\Subscription\Model\RecurringInterface;
use Sylius\Component\Subscription\Model\SchedulableInterface;

/**
 * RecurringSchedulerInterface
 *
 * Schedules schedulable classes according to recurring interval
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface RecurringSchedulerInterface
{
    /**
     * Schedules the next processing date
     *
     * @param SchedulableInterface $scheduleSubject
     * @param RecurringInterface $recurring
     */
    public function schedule(SchedulableInterface $scheduleSubject, RecurringInterface $recurring);
}
