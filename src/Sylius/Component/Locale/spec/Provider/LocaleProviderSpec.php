<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Locale\Provider;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Locale\Provider\LocaleProvider;
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class LocaleProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $localeRepository)
    {
        $this->beConstructedWith($localeRepository, 'pl_PL');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LocaleProvider::class);
    }

    function it_is_a_locale_provider_interface()
    {
        $this->shouldImplement(LocaleProviderInterface::class);
    }

    function it_returns_all_enabled_locales(RepositoryInterface $localeRepository, LocaleInterface $locale)
    {
        $localeRepository->findAll()->willReturn([$locale]);
        $locale->getCode()->willReturn('en_US');

        $this->getAvailableLocalesCodes()->shouldReturn(['en_US']);
    }

    function it_returns_the_default_locale()
    {
        $this->getDefaultLocaleCode()->shouldReturn('pl_PL');
    }
}
