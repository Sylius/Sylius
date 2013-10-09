<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace Sylius\Bundle\SubscriptionBundle\EventListener;


use Sylius\Bundle\SubscriptionBundle\Event\SubscriptionEvent;
use Sylius\Bundle\SubscriptionBundle\Model\RecurringSubscriptionInterface;
use Sylius\Bundle\SubscriptionBundle\Scheduler\RecurringSchedulerInterface;

/**
 * RecurringSchedulerListener
 *
 * Listens to subscription processing event and delegates scheduling of next processing date to scheduler
 *
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class RecurringSchedulerListener
{
    /**
     * @var RecurringSchedulerInterface
     */
    protected $scheduler;

    public function __construct(RecurringSchedulerInterface $scheduler)
    {
        $this->scheduler = $scheduler;
    }

    public function onSuccess(SubscriptionEvent $event)
    {
        $subscription = $event->getSubscription();

        if (!$subscription instanceof RecurringSubscriptionInterface) {
            throw new \InvalidArgumentException('Subscription must implement RecurringSubscriptionInterface');
        }

        $this->scheduler->schedule($subscription, $subscription);
    }
}