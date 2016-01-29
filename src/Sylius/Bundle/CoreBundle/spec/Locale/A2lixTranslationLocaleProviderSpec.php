<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Locale;

use A2lix\TranslationFormBundle\Locale\LocaleProviderInterface as A2lixLocaleProviderInterface;
use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;

/**
 * @mixin \Sylius\Bundle\CoreBundle\Locale\A2lixTranslationLocaleProvider
 *
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class A2lixTranslationLocaleProviderSpec extends ObjectBehavior
{
    function let(LocaleProviderInterface $syliusLocaleProvider, LocaleContextInterface $syliusLocaleContext)
    {
        $this->beConstructedWith($syliusLocaleProvider, $syliusLocaleContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Locale\A2lixTranslationLocaleProvider');
    }

    function it_is_a2lix_translation_form_bundle_locale_provider()
    {
        $this->shouldImplement(A2lixLocaleProviderInterface::class);
    }

    function it_returns_list_of_locales(LocaleProviderInterface $syliusLocaleProvider)
    {
        $listOfLocales = ['en_US', 'pl_PL'];

        $syliusLocaleProvider->getAvailableLocales()->shouldBeCalled()->willReturn($listOfLocales);

        $this->getLocales()->shouldReturn($listOfLocales);
    }

    function it_returns_default_locale(LocaleContextInterface $syliusLocaleContext)
    {
        $syliusLocaleContext->getDefaultLocale()->shouldBeCalled()->willReturn('pl_PL');

        $this->getDefaultLocale()->shouldReturn('pl_PL');
    }

    function it_returns_required_locales(LocaleContextInterface $syliusLocaleContext)
    {
        $syliusLocaleContext->getDefaultLocale()->shouldBeCalled()->willReturn('pl_PL');

        $this->getRequiredLocales()->shouldReturn(['pl_PL']);
    }
}
