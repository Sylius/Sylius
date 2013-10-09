<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\Scheduler;


use Sylius\Bundle\SubscriptionBundle\Model\RecurringInterface;
use Sylius\Bundle\SubscriptionBundle\Model\SchedulableInterface;

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