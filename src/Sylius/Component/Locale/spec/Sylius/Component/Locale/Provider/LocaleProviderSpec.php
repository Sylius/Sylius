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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
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

    function it_implements_Sylius_locale_provider_interface()
    {
        $this->shouldImplement('Sylius\Component\Locale\Provider\LocaleProviderInterface');
    }

    function it_returns_all_enabled_locales(LocaleInterface $locale, $localeRepository)
    {
        $localeRepository->findBy(array('enabled' => true))->shouldBeCalled()->willReturn(array($locale));

        $this->getAvailableLocales()->shouldReturn(array($locale));
    }
}
