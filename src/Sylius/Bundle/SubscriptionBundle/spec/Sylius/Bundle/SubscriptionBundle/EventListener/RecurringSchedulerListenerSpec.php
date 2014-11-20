<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Bundle\SubscriptionBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Subscription\Event\SubscriptionEvent;
use Sylius\Component\Subscription\Model\RecurringSubscriptionInterface;
use Sylius\Component\Subscription\Scheduler\RecurringSchedulerInterface;

class RecurringSchedulerListenerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SubscriptionBundle\EventListener\RecurringSchedulerListener');
    }

    public function let(RecurringSchedulerInterface $scheduler)
    {
        $this->beConstructedWith($scheduler);
    }

    public function it_decrements_max_cycles(
        RecurringSubscriptionInterface $subscription,
        SubscriptionEvent $event,
        RecurringSchedulerInterface $scheduler
    ) {
        $event->getSubscription()->willReturn($subscription);
        $scheduler->schedule($subscription, $subscription)->shouldBeCalled();

        $this->onSuccess($event);
    }
}
