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
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class SwiftMailerAdapterSpec extends ObjectBehavior
{
    public function let(\Swift_Mailer $mailer)
    {
        $this->beConstructedWith($mailer);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MailerBundle\Sender\Adapter\SwiftMailerAdapter');
    }

    public function it_is_an_adapter()
    {
        $this->shouldHaveType('Sylius\Component\Mailer\Sender\Adapter\AbstractAdapter');
    }

    public function it_sends_an_email(
        $mailer,
        EventDispatcherInterface $dispatcher,
        RenderedEmail $renderedEmail,
        EmailInterface $email
    ) {
        $this->setEventDispatcher($dispatcher);

        $renderedEmail->getSubject()->shouldBeCalled()->willReturn('subject');
        $renderedEmail->getBody()->shouldBeCalled()->willReturn('body');

        $dispatcher->dispatch(
            SyliusMailerEvents::EMAIL_PRE_SEND,
            Argument::type('Sylius\Component\Mailer\Event\EmailSendEvent')
        )->shouldBeCalled();

        $mailer->send(Argument::type('\Swift_Message'))->shouldBeCalled();

        $dispatcher->dispatch(
            SyliusMailerEvents::EMAIL_POST_SEND,
            Argument::type('Sylius\Component\Mailer\Event\EmailSendEvent')
        )->shouldBeCalled();

        $this->send(
            array('pawel@sylius.org'),
            'arnaud@sylius.org',
            'arnaud',
            $renderedEmail,
            $email,
            array()
        );
    }
}
