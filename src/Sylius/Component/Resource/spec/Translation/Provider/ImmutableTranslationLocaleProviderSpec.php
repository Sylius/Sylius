<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Translation\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Translation\Provider\ImmutableTranslationLocaleProvider;
use Sylius\Component\Resource\Translation\Provider\TranslationLocaleProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ImmutableTranslationLocaleProviderSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(['pl_PL', 'en_US'], 'pl_PL');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImmutableTranslationLocaleProvider::class);
    }

    function it_implements_translation_locale_provider_interface()
    {
        $this->shouldImplement(TranslationLocaleProviderInterface::class);
    }

    function it_returns_defined_locales_codes()
    {
        $this->getDefinedLocalesCodes()->shouldReturn(['pl_PL', 'en_US']);
    }

    function it_returns_the_default_locale_code()
    {
        $this->getDefaultLocaleCode()->shouldReturn('pl_PL');
    }
}
