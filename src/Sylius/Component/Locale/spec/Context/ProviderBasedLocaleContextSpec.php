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

namespace spec\Sylius\Component\Locale\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

final class ProviderBasedLocaleContextSpec extends ObjectBehavior
{
    function let(LocaleProviderInterface $localeProvider): void
    {
        $this->beConstructedWith($localeProvider);
    }

    function it_is_a_locale_context(): void
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    function it_returns_the_channels_default_locale(LocaleProviderInterface $localeProvider): void
    {
        $localeProvider->getAvailableLocalesCodes()->willReturn(['pl_PL', 'en_US']);
        $localeProvider->getDefaultLocaleCode()->willReturn('pl_PL');

        $this->getLocaleCode()->shouldReturn('pl_PL');
    }

    function it_throws_a_locale_not_found_exception_if_default_locale_is_not_available(
        LocaleProviderInterface $localeProvider
    ): void {
        $localeProvider->getAvailableLocalesCodes()->willReturn(['es_ES', 'en_US']);
        $localeProvider->getDefaultLocaleCode()->willReturn('pl_PL');

        $this->shouldThrow(LocaleNotFoundException::class)->during('getLocaleCode');
    }
}
