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

class RecurringSchedulerListenerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SubscriptionBundle\EventListener\RecurringSchedulerListener');
    }

    /**
     * @param Sylius\Bundle\SubscriptionBundle\Scheduler\RecurringSchedulerInterface $scheduler
     */
    function let($scheduler)
    {
        $this->beConstructedWith($scheduler);
    }

    /**
     * @param Sylius\Bundle\SubscriptionBundle\Model\RecurringSubscriptionInterface $subscription
     * @param Symfony\Component\EventDispatcher\GenericEvent $event
     */
    function it_decrements_max_cycles($subscription, $event, $scheduler)
    {
        $event->getSubject()->willReturn($subscription);
        $scheduler->schedule($subscription, $subscription)->shouldBeCalled();

        $this->onSubscriptionProcessing($event);
    }
}