<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MailerBundle\Sender\Adapter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\MailerBundle\Sender\Adapter\SwiftMailerAdapter;
use Sylius\Component\Mailer\Event\EmailSendEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class SwiftMailerAdapterSpec extends ObjectBehavior
{
    function let(\Swift_Mailer $mailer)
    {
        $this->beConstructedWith($mailer);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(SwiftMailerAdapter::class);
    }

    function it_is_an_adapter()
    {
        $this->shouldHaveType(AbstractAdapter::class);
    }

    function it_sends_an_email(
        \Swift_Mailer $mailer,
        EmailInterface $email,
        EventDispatcherInterface $dispatcher,
        RenderedEmail $renderedEmail
    ) {
        $this->setEventDispatcher($dispatcher);

        $renderedEmail->getSubject()->shouldBeCalled()->willReturn('subject');
        $renderedEmail->getBody()->shouldBeCalled()->willReturn('body');

        $dispatcher->dispatch(
            SyliusMailerEvents::EMAIL_PRE_SEND,
            Argument::type(EmailSendEvent::class)
        )->shouldBeCalled();

        $mailer->send(Argument::type('\Swift_Message'))->shouldBeCalled();

        $dispatcher->dispatch(
            SyliusMailerEvents::EMAIL_POST_SEND,
            Argument::type(EmailSendEvent::class)
        )->shouldBeCalled();

        $this->send(
            ['pawel@sylius.org'],
            'arnaud@sylius.org',
            'arnaud',
            $renderedEmail,
            $email,
            []
        );
    }
}
