<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Contact\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Contact\Model\RequestInterface;
use Sylius\Component\Contact\Model\TopicInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class RequestSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Contact\Model\Request');
    }

    function it_implements_Sylius_contact_request_interface()
    {
        $this->shouldImplement(RequestInterface::class);
    }

    function it_has_no_id_by_default()
    {
        $this->getId()->shouldReturn(null);
    }

    function it_has_no_first_name_by_default()
    {
        $this->getFirstName()->shouldReturn(null);
    }

    function its_first_name_is_mutable()
    {
        $this->setFirstName('Michal');
        $this->getFirstName()->shouldReturn('Michal');
    }

    function it_has_no_last_name_by_default()
    {
        $this->getLastName()->shouldReturn(null);
    }

    function its_last_name_is_mutable()
    {
        $this->setLastName('lastname');
        $this->getLastName()->shouldReturn('lastname');
    }

    function it_has_no_email_by_default()
    {
        $this->getEmail()->shouldReturn(null);
    }

    function its_email_is_mutable()
    {
        $this->setEmail('michal@lakion.com');
        $this->getEmail()->shouldReturn('michal@lakion.com');
    }

    function it_has_no_message_by_default()
    {
        $this->getMessage()->shouldReturn(null);
    }

    function its_message_is_mutable()
    {
        $this->setMessage('hello');
        $this->getMessage()->shouldReturn('hello');
    }

    function it_has_no_topic_by_default()
    {
        $this->getTopic()->shouldReturn(null);
    }

    function its_topic_is_mutable(TopicInterface $topic)
    {
        $this->setTopic($topic);
        $this->getTopic()->shouldReturn($topic);
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType(\DateTime::class);
    }

    function its_creation_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setCreatedAt($date);
        $this->getCreatedAt()->shouldReturn($date);
    }

    function it_has_no_last_update_date_by_default()
    {
        $this->getUpdatedAt()->shouldReturn(null);
    }

    function its_last_update_date_is_mutable()
    {
        $date = new \DateTime();

        $this->setUpdatedAt($date);
        $this->getUpdatedAt()->shouldReturn($date);
    }
}
