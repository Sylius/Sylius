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

namespace spec\Sylius\Bundle\MailerBundle\Renderer\Adapter;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Mailer\Event\EmailRenderEvent;
use Sylius\Component\Mailer\Model\EmailInterface;
use Sylius\Component\Mailer\Renderer\Adapter\AbstractAdapter;
use Sylius\Component\Mailer\Renderer\RenderedEmail;
use Sylius\Component\Mailer\SyliusMailerEvents;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

final class EmailTwigAdapterSpec extends ObjectBehavior
{
    function let(\Twig_Environment $twig): void
    {
        $this->beConstructedWith($twig);
    }

    function it_is_an_adapter(): void
    {
        $this->shouldHaveType(AbstractAdapter::class);
    }

    function it_renders_an_email(
        \Twig_Environment $twig,
        \Twig_Template $template,
        EmailInterface $email,
        EmailRenderEvent $event,
        EventDispatcherInterface $dispatcher,
        RenderedEmail $renderedEmail
    ): void {
        $this->setEventDispatcher($dispatcher);

        $twig->mergeGlobals([])->shouldBeCalled()->willReturn([]);

        $email->getTemplate()->shouldBeCalled()->willReturn('MyTemplate');
        $twig->loadTemplate('MyTemplate')->shouldBeCalled()->willReturn($template);

        $template->renderBlock('subject', [])->willReturn('template');
        $template->renderBlock('body', [])->willReturn('body');

        $dispatcher->dispatch(
            SyliusMailerEvents::EMAIL_PRE_RENDER,
            Argument::type(EmailRenderEvent::class)
        )->shouldBeCalled()->willReturn($event);

        $event->getRenderedEmail()->shouldBeCalled()->willReturn($renderedEmail);

        $this->render($email, [])->shouldReturn($renderedEmail);
    }

    function it_creates_and_renders_an_email(
        EmailInterface $email,
        EmailRenderEvent $event,
        EventDispatcherInterface $dispatcher,
        RenderedEmail $renderedEmail
    ): void {
        $this->setEventDispatcher($dispatcher);

        $email->getTemplate()->shouldBeCalled()->willReturn(null);
        $email->getSubject()->shouldBeCalled()->willReturn('subject');
        $email->getContent()->shouldBeCalled()->willReturn('content');

        $dispatcher->dispatch(
            SyliusMailerEvents::EMAIL_PRE_RENDER,
            Argument::type(EmailRenderEvent::class)
        )->shouldBeCalled()->willReturn($event);

        $event->getRenderedEmail()->shouldBeCalled()->willReturn($renderedEmail);

        $this->render($email, [])->shouldReturn($renderedEmail);
    }
}
