<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
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
        $requestStack->getMainRequest()->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_throws_locale_not_found_exception_if_locale_from_main_request_preferred_language_cannot_be_resolved(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
    ): void {
        $request = new Request();
        $request->headers->set('Accept-Language', 'fr_FR');

        $requestStack->getMainRequest()->willReturn($request);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_resolves_locale_from_main_request_preferred_language_in_locale_syntax(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
    ): void {
        $request = new Request();
        $request->headers->set('Accept-Language', 'de_DE');

        $requestStack->getMainRequest()->willReturn($request);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $this->getLocaleCode()->shouldReturn('de_DE');
    }

    function it_resolves_locale_from_main_request_preferred_language_in_mixed_cased_language_syntax(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
    ): void {
        $request = new Request();
        $request->headers->set('Accept-Language', 'dE-De');

        $requestStack->getMainRequest()->willReturn($request);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $this->getLocaleCode()->shouldReturn('de_DE');
    }

    function it_resolves_locale_from_main_request_preferred_language_in_upper_cased_language_syntax(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
    ): void {
        $request = new Request();
        $request->headers->set('Accept-Language', 'DE-DE');

        $requestStack->getMainRequest()->willReturn($request);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $this->getLocaleCode()->shouldReturn('de_DE');
    }

    function it_resolves_locale_from_main_request_preferred_language_in_lower_cased_language_syntax(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
    ): void {
        $request = new Request();
        $request->headers->set('Accept-Language', 'de-de');

        $requestStack->getMainRequest()->willReturn($request);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $this->getLocaleCode()->shouldReturn('de_DE');
    }
}
