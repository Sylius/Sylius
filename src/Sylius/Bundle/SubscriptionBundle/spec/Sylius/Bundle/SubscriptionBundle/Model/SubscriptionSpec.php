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
class SubscriptionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SubscriptionBundle\Model\Subscription');
    }

    function it_implements_Sylius_subscription_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SubscriptionBundle\Model\SubscriptionInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_initializes_items()
    {
        $this->getItems()->shouldImplement('Doctrine\Common\Collections\ArrayCollection');
    }

    function it_has_no_scheduled_date_by_default()
    {
        $this->getScheduledDate()->shouldReturn(null);
    }

    function its_scheduled_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setScheduledDate($date);
        $this->getScheduledDate()->shouldReturn($date);
    }

    function it_has_no_processed_date_by_default()
    {
        $this->getProcessedDate()->shouldReturn(null);
    }

    function its_processed_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setProcessedDate($date);
        $this->getProcessedDate()->shouldReturn($date);
    }

    function it_has_no_items_by_default()
    {
        $this->countItems()->shouldReturn(0);
    }

    /**
     * @param Sylius\Bundle\SubscriptionBundle\Model\SubscriptionItemInterface $item
     */
    function it_adds_items_properly($item)
    {
        $this->countItems()->shouldReturn(0);

        $item->setSubscription($this)->shouldBeCalled();
        $this->addItem($item)->shouldReturn($this);

        $this->getItems()->first()->shouldReturn($item);
        $this->countItems()->shouldReturn(1);
    }

    /**
     * @param Sylius\Bundle\SubscriptionBundle\Model\SubscriptionItemInterface $item
     */
    function it_removes_items_properly($item)
    {
        $item->setSubscription($this)->shouldBeCalled();
        $this->addItem($item);

        $item->setSubscription(null)->shouldBeCalled();
        $this->removeItem($item)->shouldReturn($this);
        $this->countItems()->shouldReturn(0);
    }
}