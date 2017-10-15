<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Mailer\Model;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Mailer\Model\EmailInterface;

final class EmailSpec extends ObjectBehavior
{
    function it_implements_email_interface(): void
    {
        $this->shouldImplement(EmailInterface::class);
    }

    function it_has_no_code_by_default(): void
    {
        $this->getCode()->shouldReturn(null);
    }

    function its_code_is_mutable(): void
    {
        $this->setCode('bar');
        $this->getCode()->shouldReturn('bar');
    }

    function its_subject_is_mutable(): void
    {
        $this->setSubject('foo');
        $this->getSubject()->shouldReturn('foo');
    }

    function its_content_is_mutable(): void
    {
        $this->setContent('foo content');
        $this->getContent()->shouldReturn('foo content');
    }

    function its_template_is_mutable(): void
    {
        $this->setContent('template.html.twig');
        $this->getContent()->shouldReturn('template.html.twig');
    }

    function its_sender_name_is_mutable(): void
    {
        $this->setSenderName('Example');
        $this->getSenderName()->shouldReturn('Example');
    }

    function its_sender_address_is_mutable(): void
    {
        $this->setSenderAddress('no-reply@example.com');
        $this->getSenderAddress()->shouldReturn('no-reply@example.com');
    }

    function it_is_enabled_by_default(): void
    {
        $this->shouldBeEnabled();
    }

    function it_can_be_disabled(): void
    {
        $this->setEnabled(false);
        $this->shouldNotBeEnabled();
    }
}
