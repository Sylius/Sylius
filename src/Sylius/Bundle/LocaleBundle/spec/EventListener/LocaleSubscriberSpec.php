<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\LocaleBundle\EventListener;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\KernelEvents;

class LocaleSubscriberSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext)
    {
        $this->beConstructedWith($localeContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\LocaleBundle\EventListener\LocaleSubscriber');
    }

    function it_is_a_subscriber()
    {
        $this->shouldImplement(EventSubscriberInterface::class);
    }

    function it_subscribes_to_event()
    {
        $this::getSubscribedEvents()->shouldReturn([
            KernelEvents::REQUEST => [['onKernelRequest', 35]],
        ]);
    }

    function it_set_the_locale_to_the_request($localeContext, GetResponseEvent $event, Request $request)
    {
        $localeContext->getCurrentLocale()->willReturn('fr_FR');
        $event->getRequest()->willReturn($request);
        $request->hasPreviousSession()->shouldBeCalled()->willReturn(true);
        $request->setLocale('fr_FR')->shouldBeCalled();

        $this->onKernelRequest($event);
    }

    function it_set_the_default_locale_to_the_request($localeContext, GetResponseEvent $event, Request $request)
    {
        $localeContext->getCurrentLocale()->willReturn(null);
        $localeContext->getDefaultLocale()->willReturn('fr_FR');
        $event->getRequest()->willReturn($request);
        $request->hasPreviousSession()->shouldBeCalled()->willReturn(true);
        $request->setLocale('fr_FR')->shouldBeCalled();

        $this->onKernelRequest($event);
    }

    function it_do_not_set_the_locale_because_the_session_is_not_started($localeContext, GetResponseEvent $event, Request $request)
    {
        $localeContext->getCurrentLocale()->willReturn('fr_FR');
        $event->getRequest()->willReturn($request);
        $request->hasPreviousSession()->shouldBeCalled()->willReturn(false);

        $this->onKernelRequest($event);
    }
}
