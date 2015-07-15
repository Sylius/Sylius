<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MailerBundle\Renderer\Adapter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Event\EmailRenderEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TwigAdapterSpec extends ObjectBehavior
{
    public function let(\Twig_Environment $twig)
    {
        $this->beConstructedWith($twig);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MailerBundle\Renderer\Adapter\TwigAdapter');
    }

    public function it_is_an_adapter()
    {
        $this->shouldHaveType('Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter');
    }

    public function it_renders_an_email(
        $twig,
        EmailInterface $email,
        \Twig_Template $template,
        EventDispatcherInterface $dispatcher,
        EmailRenderEvent $event,
        RenderedEmail $renderedEmail
    ) {
        $this->setEventDispatcher($dispatcher);

        $twig->mergeGlobals(array())->shouldBeCalled()->willReturn(array());

        $email->getTemplate()->shouldBeCalled()->willReturn('MyTemplate');
        $twig->loadTemplate('MyTemplate')->shouldBeCalled()->willReturn($template);

        $template->renderBlock('subject', array())->shouldBeCalled();
        $template->renderBlock('body', array())->shouldBeCalled();

        $dispatcher->dispatch(
            SyliusMailerEvents::EMAIL_PRE_RENDER,
            Argument::type('Sylius\Component\Mailer\Event\EmailRenderEvent')
        )->shouldBeCalled()->willReturn($event);

        $event->getRenderedEmail()->shouldBeCalled()->willReturn($renderedEmail);

        $this->render($email, array())->shouldReturn($renderedEmail);
    }

    public function it_creates_and_renders_an_email(
        EmailInterface $email,
        EventDispatcherInterface $dispatcher,
        EmailRenderEvent $event,
        RenderedEmail $renderedEmail
    ) {
        $this->setEventDispatcher($dispatcher);

        $email->getTemplate()->shouldBeCalled()->willReturn(null);
        $email->getSubject()->shouldBeCalled()->willReturn('subject');
        $email->getContent()->shouldBeCalled()->willReturn('content');

        $dispatcher->dispatch(
            SyliusMailerEvents::EMAIL_PRE_RENDER,
            Argument::type('Sylius\Component\Mailer\Event\EmailRenderEvent')
        )->shouldBeCalled()->willReturn($event);

        $event->getRenderedEmail()->shouldBeCalled()->willReturn($renderedEmail);

        $this->render($email, array())->shouldReturn($renderedEmail);
    }
}
