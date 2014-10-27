<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Component\Subscription\Scheduler;

use PhpSpec\ObjectBehavior;
use spec\Sylius\Bundle\SubscriptionBundle\Scheduler\Sylius;
use Sylius\Component\Subscription\Model\SchedulableInterface;
use Sylius\Component\Subscription\Model\RecurringInterface;

class RecurringSchedulerSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Subscription\Scheduler\RecurringScheduler');
    }

    public function it_implements_Sylius_recurring_scheduler_interface()
    {
        $this->shouldImplement('Sylius\Component\Subscription\Scheduler\RecurringSchedulerInterface');
    }

    public function it_should_schedule_properly(
        SchedulableInterface $schedulable,
        RecurringInterface $recurring
    ) {
        $recurring->getIntervalUnit()->willReturn('days');
        $recurring->getIntervalFrequency()->willReturn(5);

        $date = new \DateTime('+5 days');

        $schedulable->setScheduledDate($date)->shouldBeCalled();

        $this->schedule($schedulable, $recurring);
    }
}
