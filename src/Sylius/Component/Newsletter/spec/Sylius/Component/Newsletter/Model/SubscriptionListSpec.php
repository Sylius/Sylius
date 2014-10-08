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
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Newsletter\Model\SubscriptionList');
    }

    public function it_implements_Sylius_SubscriptionList_interface()
    {
        $this->shouldImplement('Sylius\Component\Newsletter\Model\SubscriptionListInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_name_by_default()
    {
        $this->getName()->shouldReturn(null);
    }

    public function its_name_is_mutable()
    {
        $this->setName('Newsletter');
        $this->getName()->shouldReturn('Newsletter');
    }

    public function it_has_no_description_by_default()
    {
        $this->getDescription()->shouldReturn(null);
    }

    public function its_description_is_mutable()
    {
        $this->setDescription('Newsletter subscription list');
        $this->getDescription()->shouldReturn('Newsletter subscription list');
    }

    public function it_creates_subscribers_collection_by_default()
    {
        $this->getSubscribers()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function it_adds_subscribers_properly(SubscriberInterface $subscriberInterface)
    {
        $this->hasSubscriber($subscriberInterface)->shouldReturn(false);

        $this->addSubscriber($subscriberInterface);
        $subscriberInterface->addSubscriptionList($this)->shouldBeCalled();
        $this->hasSubscriber($subscriberInterface)->shouldReturn(true);
    }

    public function it_removes_subscribers_properly(SubscriberInterface $subscriberInterface)
    {
        $this->hasSubscriber($subscriberInterface)->shouldReturn(false);

        $this->addSubscriber($subscriberInterface);
        $this->hasSubscriber($subscriberInterface)->shouldReturn(true);

        $this->removeSubscriber($subscriberInterface);
        $subscriberInterface->removeSubscriptionList($this)->shouldBeCalled();
        $this->hasSubscriber($subscriberInterface)->shouldReturn(false);
    }
}
