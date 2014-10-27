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
use Prophecy\Argument;
use Doctrine\Common\Persistence\ObjectManager;
use Sylius\Component\Subscription\Event\SubscriptionEvent;
use Sylius\Component\Subscription\Model\RecurringSubscriptionInterface;

class MaxCyclesListenerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SubscriptionBundle\EventListener\MaxCyclesListener');
    }

    public function let(ObjectManager $manager)
    {
        $this->beConstructedWith($manager);
    }

    public function it_decrements_max_cycles(
        RecurringSubscriptionInterface $subscription,
        SubscriptionEvent $event,
        ObjectManager $manager
    ) {
        $subscription->getMaxCycles()->willReturn(5);
        $subscription->setMaxCycles(4)->shouldBeCalled();
        $event->getSubscription()->willReturn($subscription);

        $manager->remove($subscription)->shouldNotBeCalled();

        $this->onSuccess($event);
    }

    public function it_does_nothing_when_max_cycles_is_null(
        RecurringSubscriptionInterface $subscription,
        SubscriptionEvent $event,
        ObjectManager $manager
    ) {
        $subscription->getMaxCycles()->willReturn(null);
        $subscription->setMaxCycles(Argument::any())->shouldNotBeCalled();
        $event->getSubscription()->willReturn($subscription);

        $manager->remove($subscription)->shouldNotBeCalled();

        $this->onSuccess($event);
    }

    public function it_removes_subscription_when_max_cycles_hits_zero(
        RecurringSubscriptionInterface $subscription,
        SubscriptionEvent $event,
        ObjectManager $manager
    ) {
        $subscription->getMaxCycles()->willReturn(0);
        $subscription->setMaxCycles(0)->shouldBeCalled();
        $event->getSubscription()->willReturn($subscription);

        $manager->remove($subscription)->shouldBeCalled();

        $this->onSuccess($event);
    }
}
