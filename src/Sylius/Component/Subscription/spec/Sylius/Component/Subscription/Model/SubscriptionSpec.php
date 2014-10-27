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
use Sylius\Component\Subscription\Model\SubscriptionItemInterface;

/**
 * @author Daniel Richter <nexyz9@gmail.com>
 */
class SubscriptionSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Subscription\Model\Subscription');
    }

    public function it_implements_Sylius_subscription_interface()
    {
        $this->shouldImplement('Sylius\Component\Subscription\Model\SubscriptionInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_initializes_items()
    {
        $this->getItems()->shouldImplement('Doctrine\Common\Collections\ArrayCollection');
    }

    public function it_has_no_scheduled_date_by_default()
    {
        $this->getScheduledDate()->shouldReturn(null);
    }

    public function its_scheduled_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setScheduledDate($date);
        $this->getScheduledDate()->shouldReturn($date);
    }

    public function it_has_no_processed_date_by_default()
    {
        $this->getProcessedDate()->shouldReturn(null);
    }

    public function its_processed_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setProcessedDate($date);
        $this->getProcessedDate()->shouldReturn($date);
    }

    public function it_has_no_items_by_default()
    {
        $this->countItems()->shouldReturn(0);
    }

    public function it_adds_items_properly(SubscriptionItemInterface $item)
    {
        $this->countItems()->shouldReturn(0);

        $item->setSubscription($this)->shouldBeCalled();
        $this->addItem($item)->shouldReturn($this);

        $this->getItems()->first()->shouldReturn($item);
        $this->countItems()->shouldReturn(1);
    }

    public function it_removes_items_properly(SubscriptionItemInterface $item)
    {
        $item->setSubscription($this)->shouldBeCalled();
        $this->addItem($item);

        $item->setSubscription(null)->shouldBeCalled();
        $this->removeItem($item)->shouldReturn($this);
        $this->countItems()->shouldReturn(0);
    }
}
