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

    public function it_has_no_interval_unit_by_default()
    {
        $this->getIntervalUnit()->shouldReturn(null);
    }

    public function its_interval_unit_is_mutable()
    {
        $this->setIntervalUnit('days');
        $this->getIntervalUnit()->shouldReturn('days');
    }

    public function it_has_no_interval_frequency_by_default()
    {
        $this->getIntervalFrequency()->shouldReturn(null);
    }

    public function its_interval_frequency_is_mutable()
    {
        $this->setIntervalFrequency(5);
        $this->getIntervalFrequency()->shouldReturn(5);
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
