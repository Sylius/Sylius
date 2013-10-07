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


use Sylius\Bundle\SubscriptionBundle\Model\SubscriptionInterface;

/**
 * Subscription Scheduler Interface
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
interface SubscriptionSchedulerInterface
{
    /**
     * Schedules the next processing date for a Subscription
     *
     * @param SubscriptionInterface $subscription
     * @return void
     */
    public function schedule(SubscriptionInterface $subscription);
}