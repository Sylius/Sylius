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
class LimitedIntervalSubscriptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SubscriptionBundle\Model\LimitedIntervalSubscription');
    }

    function it_implements_Sylius_subscription_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SubscriptionBundle\Model\LimitedIntervalSubscriptionInterface');
    }

    function it_has_no_interval_by_default()
    {
        $this->getInterval()->shouldReturn(null);
    }

    function its_interval_is_mutable()
    {
        $this->setInterval(5);
        $this->getInterval()->shouldReturn(5);
    }

    function it_has_no_limit_by_default()
    {
        $this->getLimit()->shouldReturn(null);
    }

    function its_limit_is_mutable()
    {
        $this->setLimit(3);
        $this->getLimit()->shouldReturn(3);
    }
}