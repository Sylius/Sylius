<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Newsletter\Model;

use Sylius\Component\Newsletter\Model\SubscriberInterface;
use PhpSpec\ObjectBehavior;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class SubscriptionListSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Newsletter\Model\SubscriptionList');
    }

    function it_implements_Sylius_SubscriptionList_interface()
    {
        $this->shouldImplement('Sylius\Component\Newsletter\Model\SubscriptionListInterface');
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    function its_name_is_mutable()
    {
        $this->setName('Newsletter');
        $this->getName()->shouldReturn('Newsletter');
    }

    function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    function its_description_is_mutable()
    {
        $this->setDescription('Newsletter subscription list');
        $this->getDescription()->shouldReturn('Newsletter subscription list');
    }

    function it_creates_subscribers_collection_by_default()
    {
        $this->getSubscribers()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    function it_adds_subscribers_properly(SubscriberInterface $subscriberInterface)
    {
        $this->hasSubscriber($subscriberInterface)->shouldReturn(false);

        $this->addSubscriber($subscriberInterface);
        $subscriberInterface->addSubscriptionList($this)->shouldBeCalled();
        $this->hasSubscriber($subscriberInterface)->shouldReturn(true);
    }

    function it_removes_subscribers_properly(SubscriberInterface $subscriberInterface)
    {
        $this->hasSubscriber($subscriberInterface)->shouldReturn(false);

        $this->addSubscriber($subscriberInterface);
        $this->hasSubscriber($subscriberInterface)->shouldReturn(true);

        $this->removeSubscriber($subscriberInterface);
        $subscriberInterface->removeSubscriptionList($this)->shouldBeCalled();
        $this->hasSubscriber($subscriberInterface)->shouldReturn(false);
    }

}
