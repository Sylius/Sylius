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
use Sylius\Component\Subscription\Model\SchedulableInterface;
use Sylius\Component\Subscription\Model\RecurringSubscriptionInterface;

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
        RecurringSubscriptionInterface $recurring
    ) {
        $interval = new \DateInterval('P3D');
        $recurring->getInterval()->willReturn($interval);

        $date = new \DateTime('+3 days');

        $schedulable->setScheduledDate($date)->shouldBeCalled();

        $this->schedule($schedulable, $recurring);
    }
}
