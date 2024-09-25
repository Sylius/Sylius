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

namespace spec\Sylius\Bundle\LocaleBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelperInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Context\LocaleNotFoundException;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;

final class LocaleHelperSpec extends ObjectBehavior
{
    function let(
        LocaleConverterInterface $localeConverter,
        LocaleContextInterface $localeContext,
    ): void {
        $this->beConstructedWith($localeConverter, $localeContext);
    }

    function it_is_a_locale_helper(): void
    {
        $this->shouldImplement(LocaleHelperInterface::class);
    }

    function it_converts_locales_code_to_name_using_default_locale_when_context_has_none(
        LocaleConverterInterface $localeConverter,
        LocaleContextInterface $localeContext,
    ): void {
        $localeContext->getLocaleCode()->willThrow(LocaleNotFoundException::class);
        $localeConverter->convertCodeToName('fr_FR', null)->willReturn('French (France)');

        $this->convertCodeToName('fr_FR')->shouldReturn('French (France)');
    }

    function it_converts_locales_code_to_name_using_locale_from_context(
        LocaleConverterInterface $localeConverter,
        LocaleContextInterface $localeContext,
    ): void {
        $localeContext->getLocaleCode()->willReturn('en');
        $localeConverter->convertCodeToName('fr_FR', 'en')->willReturn('French (France)');

        $this->convertCodeToName('fr_FR')->shouldReturn('French (France)');
    }

    function it_converts_locales_code_to_name_using_given_locale(
        LocaleConverterInterface $localeConverter,
        LocaleContextInterface $localeContext,
    ): void {
        $localeContext->getLocaleCode()->shouldNotBeCalled();
        $localeConverter->convertCodeToName('en', 'pl')->willReturn('angielski');

        $this->convertCodeToName('en', 'pl')->shouldReturn('angielski');
    }

    function it_fallbacks_to_the_code_if_the_name_is_not_in_the_database_and_no_locale_is_in_context(
        LocaleConverterInterface $localeConverter,
        LocaleContextInterface $localeContext,
    ): void {
        $localeContext->getLocaleCode()->willThrow(LocaleNotFoundException::class);
        $localeConverter->convertCodeToName('en_DG', null)->willThrow(new \InvalidArgumentException());

        $this->convertCodeToName('en_DG')->shouldReturn('en_DG');
    }

    function it_fallbacks_to_the_code_if_the_name_is_not_in_the_database_and_a_locale_is_in_context(
        LocaleConverterInterface $localeConverter,
        LocaleContextInterface $localeContext,
    ): void {
        $localeContext->getLocaleCode()->willReturn('en');
        $localeConverter->convertCodeToName('en_DG', 'en')->willThrow(new \InvalidArgumentException());

        $this->convertCodeToName('en_DG')->shouldReturn('en_DG');
    }

    function it_has_a_name(): void
    {
        $this->getName()->shouldReturn('sylius_locale');
    }
}
