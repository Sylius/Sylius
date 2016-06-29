<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Resource\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Resource\Provider\LocaleProviderInterface;

/**
 * @author Anna Walasek <anna.walasek@lakion.com>
 */
class LocaleProviderSpec extends  ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('pl_PL', 'en_US', 'en_GB');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Resource\Provider\LocaleProvider');
    }

    function it_implements_locale_provider_interface()
    {
        $this->shouldImplement(LocaleProviderInterface::class);
    }

    function it_provides_current_locale()
    {
        $this->getCurrentLocale()->shouldReturn('pl_PL');
    }

    function it_provides_fallback_locale()
    {
        $this->getFallbackLocale()->shouldReturn('en_US');
    }

    function it_provides_default_locale()
    {
        $this->getDefaultLocale()->shouldReturn('en_GB');
    }

    function it_return_fallback_locale_if_default_locale_is_not_set()
    {
        $this->beConstructedWith('pl_PL', 'en_US');

        $this->getDefaultLocale()->shouldReturn('en_US');
    }
}
