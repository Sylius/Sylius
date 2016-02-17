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
use Sylius\Component\Locale\Provider\LocaleProviderInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @mixin \Sylius\Component\Locale\Provider\LocaleProvider
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
class LocaleProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $localeRepository)
    {
        $this->beConstructedWith($localeRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Locale\Provider\LocaleProvider');
    }

    function it_is_Sylius_locale_provider()
    {
        $this->shouldImplement(LocaleProviderInterface::class);
    }

    function it_returns_available_locales_codes(
        RepositoryInterface $localeRepository,
        LocaleInterface $firstLocale,
        LocaleInterface $secondLocale
    ) {
        $locales = [$firstLocale, $secondLocale];
        $localeRepository->findBy(['enabled' => true])->willReturn($locales);

        $firstLocale->getCode()->willReturn('en_US');
        $secondLocale->getCode()->willReturn('pl_PL');

        $this->getAvailableLocales()->shouldReturn(['en_US', 'pl_PL']);
    }

    function it_checks_if_the_locale_code_is_available(
        RepositoryInterface $localeRepository,
        LocaleInterface $firstLocale,
        LocaleInterface $secondLocale
    ) {
        $locales = [$firstLocale, $secondLocale];
        $localeRepository->findBy(['enabled' => true])->willReturn($locales);

        $firstLocale->getCode()->willReturn('en_US');
        $secondLocale->getCode()->willReturn('pl_PL');

        $this->isLocaleAvailable('en_US')->shouldReturn(true);
        $this->isLocaleAvailable('de_DE')->shouldReturn(false);
    }
}
