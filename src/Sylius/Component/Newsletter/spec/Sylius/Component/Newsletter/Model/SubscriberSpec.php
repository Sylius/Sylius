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

use PhpSpec\ObjectBehavior;
use Sylius\Component\Newsletter\Model\SubscriptionListInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class SubscriberSpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Newsletter\Model\Subscriber');
    }

    public function it_implements_Sylius_subscriber_interface()
    {
        $this->shouldImplement('Sylius\Component\Newsletter\Model\SubscriberInterface');
    }

    public function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    public function it_has_no_email_by_default()
    {
        $this->getEmail()->shouldReturn(null);
    }

    public function its_email_is_mutable()
    {
        $this->setEmail('michal@lakion.com');
        $this->getEmail()->shouldReturn('michal@lakion.com');
    }

    public function it_creates_subscription_lists_collection_by_default()
    {
        $this->getSubscriptionLists()->shouldHaveType('Doctrine\Common\Collections\Collection');
    }

    public function it_adds_subscription_lists_properly(SubscriptionListInterface $subscriptionListInterface)
    {
        $this->hasSubscriptionList($subscriptionListInterface)->shouldReturn(false);

        $this->addSubscriptionList($subscriptionListInterface);
        $subscriptionListInterface->addSubscriber($this)->shouldBeCalled();
        $this->hasSubscriptionList($subscriptionListInterface)->shouldReturn(true);
    }

    public function it_removes_subscription_lists_properly(SubscriptionListInterface $subscriptionListInterface)
    {
        $this->hasSubscriptionList($subscriptionListInterface)->shouldReturn(false);

        $this->addSubscriptionList($subscriptionListInterface);
        $this->hasSubscriptionList($subscriptionListInterface)->shouldReturn(true);

        $this->removeSubscriptionList($subscriptionListInterface);
        $subscriptionListInterface->removeSubscriber($this)->shouldBeCalled();
        $this->hasSubscriptionList($subscriptionListInterface)->shouldReturn(false);
    }

    public function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
    }

    public function its_creation_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    public function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    public function its_last_update_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
