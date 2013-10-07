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


use Sylius\Bundle\SubscriptionBundle\Model\LimitedIntervalSubscriptionInterface;
use Sylius\Bundle\SubscriptionBundle\Model\SubscriptionInterface;

/**
 * Subscription Scheduler
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class LimitedIntervalScheduler implements SubscriptionSchedulerInterface
{
    /**
     * {@inheritdoc}
     */
    public function schedule(SubscriptionInterface $subscription)
    {
        if (!$subscription instanceof LimitedIntervalSubscriptionInterface) {
            throw new \InvalidArgumentException('Subscription has to implement LimitedIntervalSubscriptionInterface.');
        }

        $subscription->setScheduledDate(
            new \DateTime(sprintf('+%s days', $subscription->getInterval()))
        );

        if (null !== $subscription->getLimit()) {
            $subscription->setLimit(max(0, $subscription->getLimit() - 1));
        }
    }
}