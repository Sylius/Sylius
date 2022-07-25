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

namespace spec\Sylius\Bundle\LocaleBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Symfony\Component\HttpFoundation\HeaderBag;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RequestStack;

final class RequestHeaderBasedLocaleContextSpec extends ObjectBehavior
{
    function let(RequestStack $requestStack, LocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($requestStack, $localeProvider);
    }

    function it_is_a_locale_context(): void
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    function it_throws_locale_not_found_exception_if_main_request_is_not_found(RequestStack $requestStack): void
    {
        if (\method_exists(RequestStack::class, 'getMainRequest')) {
            $requestStack->getMainRequest()->willReturn(null);
        } else {
            $requestStack->getMasterRequest()->willReturn(null);
        }

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_throws_locale_not_found_exception_if_main_request_does_not_have_accept_language_in_header(
        RequestStack $requestStack,
        Request $request,
    ): void {
        if (\method_exists(RequestStack::class, 'getMainRequest')) {
            $requestStack->getMainRequest()->willReturn($request);
        } else {
            $requestStack->getMasterRequest()->willReturn($request);
        }

        $request->headers = new HeaderBag();

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_throws_locale_not_found_exception_if_main_request_locale_code_is_not_among_available_ones(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
        Request $request,
    ): void {
        if (\method_exists(RequestStack::class, 'getMainRequest')) {
            $requestStack->getMainRequest()->willReturn($request);
        } else {
            $requestStack->getMasterRequest()->willReturn($request);
        }

        $request->headers = new HeaderBag(['ACCEPT_LANGUAGE' => 'en_US']);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_returns_main_request_locale_code(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
        Request $request,
    ): void {
        if (\method_exists(RequestStack::class, 'getMainRequest')) {
            $requestStack->getMainRequest()->willReturn($request);
        } else {
            $requestStack->getMasterRequest()->willReturn($request);
        }

        $request->headers = new HeaderBag(['Accept-Language' => 'pl_PL']);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $this->getLocaleCode()->shouldReturn('pl_PL');
    }
}
