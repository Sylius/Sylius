<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Component\Subscription\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class RecurringSubscriptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Subscription\Model\RecurringSubscription');
    }

    public function it_implements_Sylius_subscription_interface()
    {
        $this->shouldImplement('Sylius\Component\Subscription\Model\RecurringSubscriptionInterface');
    }

    public function it_has_no_interval_by_default()
    {
        $this->getInterval()->shouldReturn(null);
    }

    public function its_interval_is_mutable()
    {
        $interval = new \DateInterval('P3D');
        $this->setInterval($interval);
        $this->getInterval()->shouldReturn($interval);
    }

    public function it_has_no_max_cycles_by_default()
    {
        $this->getMaxCycles()->shouldReturn(null);
    }

    public function its_max_cycles_is_mutable()
    {
        $this->setMaxCycles(3);
        $this->getMaxCycles()->shouldReturn(3);
    }
}
