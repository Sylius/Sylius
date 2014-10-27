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
use Sylius\Component\Subscription\Model\SubscriptionInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class SubscriptionItemSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Subscription\Model\SubscriptionItem');
    }

    public function it_implements_Sylius_subscription_interface()
    {
        $this->shouldImplement('Sylius\Component\Subscription\Model\SubscriptionItemInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_subscription_by_default()
    {
        return $this->getSubscription()->shouldReturn(null);
    }

    public function its_subscription_is_mutable(SubscriptionInterface $subscription)
    {
        $this->setSubscription($subscription);

        $this->getSubscription()->shouldReturn($subscription);
    }

    public function it_has_no_quantity_by_default()
    {
        $this->getQuantity()->shouldReturn(null);
    }

    public function its_quantity_is_mutable()
    {
        $this->setQuantity(5);

        $this->getQuantity()->shouldReturn(5);
    }
}
