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
class SubscriptionItemSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SubscriptionBundle\Model\SubscriptionItem');
    }

    function it_implements_Sylius_subscription_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SubscriptionBundle\Model\SubscriptionItemInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_subscription_by_default()
    {
        return $this->getSubscription()->shouldReturn(null);
    }

    /**
     * @param Sylius\Bundle\SubscriptionBundle\Model\SubscriptionInterface $subscription
     */
    function its_subscription_is_mutable($subscription)
    {
        $this->setSubscription($subscription);

        $this->getSubscription()->shouldReturn($subscription);
    }

    function it_has_no_quantity_by_default()
    {
        $this->getQuantity()->shouldReturn(null);
    }

    function its_quantity_is_mutable()
    {
        $this->setQuantity(5);

        $this->getQuantity()->shouldReturn(5);
    }
}