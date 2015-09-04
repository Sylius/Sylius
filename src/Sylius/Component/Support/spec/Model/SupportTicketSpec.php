<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Support\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Support\Model\SupportCategoryInterface;
use Sylius\Component\Support\Model\SupportTicketInterface;

/**
 * @author Michał Marcinkowski <michal.marcinkowski@lakion.com>
 */
class SupportTicketSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Support\Model\SupportTicket');
    }

    function it_implements_sylius_support_ticket_interface()
    {
        $this->shouldImplement('Sylius\Component\Support\Model\SupportTicketInterface');
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

    function it_has_no_category_by_default()
    {
        $this->getCategory()->shouldReturn(null);
    }

    function its_category_is_mutable(SupportCategoryInterface $category)
    {
        $this->setCategory($category);
        $this->getCategory()->shouldReturn($category);
    }

    function it_initializes_creation_date_by_default()
    {
        $this->getCreatedAt()->shouldHaveType('DateTime');
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

    function it_has_open_state_by_default()
    {
        $this->getState()->shouldReturn(SupportTicketInterface::STATE_OPEN);
    }

    function its_state_is_mutable()
    {
        $this->setState(SupportTicketInterface::STATE_CLOSED);
        $this->getState()->shouldReturn(SupportTicketInterface::STATE_CLOSED);
    }
}
