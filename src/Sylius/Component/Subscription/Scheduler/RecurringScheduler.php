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

use Sylius\Component\Subscription\Model\SchedulableInterface;
use Sylius\Component\Subscription\Model\RecurringSubscriptionInterface;

/**
 * RecurringScheduler implementation
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class RecurringScheduler implements RecurringSchedulerInterface
{
    /**
     * {@inheritdoc}
     */
    public function schedule(SchedulableInterface $scheduleSubject, RecurringSubscriptionInterface $recurring)
    {
        $now = new \DateTime();
        $scheduleSubject->setScheduledDate(
            $now->add($recurring->getInterval())
        );
    }
}
