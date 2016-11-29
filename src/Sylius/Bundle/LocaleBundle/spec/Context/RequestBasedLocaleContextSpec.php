<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\LocaleBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\LocaleBundle\Context\RequestBasedLocaleContext;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class RequestBasedLocaleContextSpec extends ObjectBehavior
{
    function let(RequestStack $requestStack, LocaleProviderInterface $localeProvider)
    {
        $this->beConstructedWith($requestStack, $localeProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(RequestBasedLocaleContext::class);
    }

    function it_is_a_locale_context()
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    function it_throws_locale_not_found_exception_if_master_request_is_not_found(RequestStack $requestStack)
    {
        $requestStack->getMasterRequest()->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_throws_locale_not_found_exception_if_master_request_does_not_have_locale_attribute(
        RequestStack $requestStack,
        Request $request
    ) {
        $requestStack->getMasterRequest()->willReturn($request);

        $request->attributes = new ParameterBag();

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_throws_locale_not_found_exception_if_master_request_locale_code_is_not_among_available_ones(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
        Request $request
    ) {
        $requestStack->getMasterRequest()->willReturn($request);

        $request->attributes = new ParameterBag(['_locale' => 'en_US']);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_returns_master_request_locale_code(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
        Request $request
    ) {
        $requestStack->getMasterRequest()->willReturn($request);

        $request->attributes = new ParameterBag(['_locale' => 'pl_PL']);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $this->getLocaleCode()->shouldReturn('pl_PL');
    }
}
