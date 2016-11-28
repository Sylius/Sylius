<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Resource\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Resource\Provider\ImmutableLocaleProvider;
use Sylius\Component\Resource\Provider\LocaleProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ImmutableLocaleProviderSpec extends ObjectBehavior
{
    function let()
    {
        $locales = [
            'pl_PL' => true,
            'en_US' => false,
        ];

        $this->beConstructedWith($locales, 'pl_PL');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImmutableLocaleProvider::class);
    }

    function it_is_a_locale_provider_interface()
    {
        $this->shouldImplement(LocaleProviderInterface::class);
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
