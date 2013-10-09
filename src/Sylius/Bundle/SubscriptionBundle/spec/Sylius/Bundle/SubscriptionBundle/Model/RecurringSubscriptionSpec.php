<?php

/*
* This file is part of the Sylius package.
*
* (c) Paweł Jędrzejewski
*
* For the full copyright and license information, please view the LICENSE
* file that was distributed with this source code.
*/

namespace spec\Sylius\Bundle\SubscriptionBundle\Model;

use PhpSpec\ObjectBehavior;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class RecurringSubscriptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SubscriptionBundle\Model\RecurringSubscription');
    }

    function it_implements_Sylius_subscription_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SubscriptionBundle\Model\RecurringSubscriptionInterface');
    }

    function it_has_no_interval_unit_by_default()
    {
        $this->getIntervalUnit()->shouldReturn(null);
    }

    function its_interval_unit_is_mutable()
    {
        $this->setIntervalUnit('days');
        $this->getIntervalUnit()->shouldReturn('days');
    }

    function it_has_no_interval_frequency_by_default()
    {
        $this->getIntervalFrequency()->shouldReturn(null);
    }

    function its_interval_frequency_is_mutable()
    {
        $this->setIntervalFrequency(5);
        $this->getIntervalFrequency()->shouldReturn(5);
    }

    function it_has_no_max_cycles_by_default()
    {
        $this->getMaxCycles()->shouldReturn(null);
    }

    function its_max_cycles_is_mutable()
    {
        $this->setMaxCycles(3);
        $this->getMaxCycles()->shouldReturn(3);
    }
}