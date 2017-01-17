<?php

namespace spec\Sylius\Bundle\LocaleBundle\Listener;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\LocaleBundle\Listener\RequestLocaleSetter;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class RequestLocaleSetterSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext, LocaleProviderInterface $localeProvider)
    {
        $this->beConstructedWith($localeContext, $localeProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RequestLocaleSetter::class);
    }

    function it_sets_locale_and_default_locale_on_request(
        LocaleContextInterface $localeContext,
        LocaleProviderInterface $localeProvider,
        GetResponseEvent $event,
        Request $request
    ) {
        $event->getRequest()->willReturn($request);

        $localeContext->getLocaleCode()->willReturn('pl_PL');
        $localeProvider->getDefaultLocaleCode()->willReturn('en_US');

        $request->setLocale('pl_PL')->shouldBeCalled();
        $request->setDefaultLocale('en_US')->shouldBeCalled();

        $this->onKernelRequest($event);
    }
}
