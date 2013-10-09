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


use Sylius\Bundle\SubscriptionBundle\Model\SchedulableInterface;
use Sylius\Bundle\SubscriptionBundle\Model\RecurringInterface;

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
    public function schedule(SchedulableInterface $scheduleSubject, RecurringInterface $recurring)
    {
        $scheduleSubject->setScheduledDate(
            new \DateTime(sprintf('+%s %s', $recurring->getIntervalFrequency(), $recurring->getIntervalUnit()))
        );
    }
}