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

class MaxCyclesListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SubscriptionBundle\EventListener\MaxCyclesListener');
    }

    /**
     * @param Doctrine\Common\Persistence\ObjectManager $manager
     */
    function let($manager)
    {
        $this->beConstructedWith($manager);
    }

    /**
     * @param Sylius\Bundle\SubscriptionBundle\Model\RecurringSubscriptionInterface $subscription
     * @param Sylius\Bundle\SubscriptionBundle\Event\SubscriptionEvent $event
     */
    function it_decrements_max_cycles($subscription, $event, $manager)
    {
        $subscription->getMaxCycles()->willReturn(5);
        $subscription->setMaxCycles(4)->shouldBeCalled();
        $event->getSubscription()->willReturn($subscription);

        $manager->remove($subscription)->shouldNotBeCalled();

        $this->onSuccess($event);
    }

    /**
     * @param Sylius\Bundle\SubscriptionBundle\Model\RecurringSubscriptionInterface $subscription
     * @param Sylius\Bundle\SubscriptionBundle\Event\SubscriptionEvent $event
     */
    function it_does_nothing_when_max_cycles_is_null($subscription, $event, $manager)
    {
        $subscription->getMaxCycles()->willReturn(null);
        $subscription->setMaxCycles(Argument::any())->shouldNotBeCalled();
        $event->getSubscription()->willReturn($subscription);

        $manager->remove($subscription)->shouldNotBeCalled();

        $this->onSuccess($event);
    }

    /**
     * @param Sylius\Bundle\SubscriptionBundle\Model\RecurringSubscriptionInterface $subscription
     * @param Sylius\Bundle\SubscriptionBundle\Event\SubscriptionEvent $event
     */
    function it_removes_subscription_when_max_cycles_hits_zero($subscription, $event, $manager)
    {
        $subscription->getMaxCycles()->willReturn(0);
        $subscription->setMaxCycles(0)->shouldBeCalled();
        $event->getSubscription()->willReturn($subscription);

        $manager->remove($subscription)->shouldBeCalled();

        $this->onSuccess($event);
    }
}