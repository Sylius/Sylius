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
        $requestStack->getMainRequest()->willReturn(null);

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_throws_locale_not_found_exception_if_main_request_preferred_language_is_default_locale(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
        Request $request,
    ): void {
        $requestStack->getMainRequest()->willReturn($request);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $request->getPreferredLanguage(['FIRSTLOCALECODE', 'pl_PL', 'de_DE'])->willReturn('FIRSTLOCALECODE');

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }

    function it_returns_main_request_preferred_language(
        RequestStack $requestStack,
        LocaleProviderInterface $localeProvider,
        Request $request,
    ): void {
        $requestStack->getMainRequest()->willReturn($request);

        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'de_DE']);

        $request->getPreferredLanguage(['FIRSTLOCALECODE', 'pl_PL', 'de_DE'])->willReturn('de_DE');

        $this->getLocaleCode()->shouldReturn('de_DE');
    }
}
