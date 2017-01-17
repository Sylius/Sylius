<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Mailer\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Mailer\Model\Email;
use Sylius\Component\Mailer\Model\EmailInterface;

final class EmailSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Email::class);
    }

    function it_implements_email_interface()
    {
        $this->shouldImplement(EmailInterface::class);
    }

    function it_has_no_code_by_default()
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable()
    {
        $this->setCode('bar');
        $this->getCode()->shouldReturn('bar');
    }

    function its_subject_is_mutable()
    {
        $this->setSubject('foo');
        $this->getSubject()->shouldReturn('foo');
    }

    function its_content_is_mutable()
    {
        $this->setContent('foo content');
        $this->getContent()->shouldReturn('foo content');
    }

    function its_template_is_mutable()
    {
        $this->setContent('template.html.twig');
        $this->getContent()->shouldReturn('template.html.twig');
    }

    function its_sender_name_is_mutable()
    {
        $this->setSenderName('Example');
        $this->getSenderName()->shouldReturn('Example');
    }

    function its_sender_address_is_mutable()
    {
        $this->setSenderAddress('no-reply@example.com');
        $this->getSenderAddress()->shouldReturn('no-reply@example.com');
    }

    function it_is_enabled_by_default()
    {
        $this->shouldBeEnabled();
    }

    function it_can_be_disabled()
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }
}
