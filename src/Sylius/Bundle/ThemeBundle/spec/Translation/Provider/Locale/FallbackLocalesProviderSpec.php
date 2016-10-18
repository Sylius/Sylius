<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\ThemeBundle\Translation;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Locale\FallbackLocalesProvider;
use Sylius\Bundle\ThemeBundle\Translation\Provider\Locale\FallbackLocalesProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class FallbackLocalesProviderSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(FallbackLocalesProvider::class);
    }

    function it_is_a_fallback_locales_provider()
    {
        $this->shouldImplement(FallbackLocalesProviderInterface::class);
    }

    function it_shortens_the_current_locale()
    {
        $this->computeFallbackLocales('pl_PL', [])->shouldReturn(['pl']);
    }

    function it_uses_the_modifier_passed_with_the_current_locale()
    {
        $this->computeFallbackLocales('pl_PL@foobar', [])->shouldReturn(['pl_PL', 'pl@foobar', 'pl']);
    }

    function it_shortens_the_fallback_locales_as_well()
    {
        $this->computeFallbackLocales('pl', ['en_US'])->shouldReturn(['en_US', 'en']);
        $this->computeFallbackLocales('pl', ['en_US', 'es_ES'])->shouldReturn(['en_US', 'en', 'es_ES', 'es']);
    }

    function it_shortens_both_the_current_and_fallback_locales_and_uses_the_modifier()
    {
        $this->computeFallbackLocales('pl_PL@foobar', ['en_US', 'pt'])->shouldReturn([
            'pl_PL',
            'pl@foobar',
            'pl',
            'en_US@foobar',
            'en_US',
            'en@foobar',
            'en',
            'pt@foobar',
            'pt',
        ]);
    }
}
