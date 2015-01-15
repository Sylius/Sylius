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
use Prophecy\Argument;
use Sylius\Component\Locale\Model\LocaleInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LocaleProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository, 'en');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Locale\Provider\LocaleProvider');
    }

    function it_implements_Sylius_locale_provider_interface()
    {
        $this->shouldImplement('Sylius\Component\Locale\Provider\LocaleProviderInterface');
    }

    function it_validates_a_null_default_locale_is_given(RepositoryInterface $repository)
    {
        $this->shouldThrow('Exception')->during('__construct', array($repository, null));
    }

    function it_validates_an_empty_default_locale_is_given(RepositoryInterface $repository)
    {
        $this->shouldThrow('Exception')->during('__construct', array($repository, ''));
    }

    function it_has_required_locales()
    {
        $this->getRequiredLocales()->shouldReturn(array('en'));
    }

    function it_returns_all_enabled_locales(LocaleInterface $locale, RepositoryInterface $repository)
    {
        $repository->findBy(array('enabled' => true))->shouldBeCalled()->willReturn(array($locale));

        $this->getAvailableLocales()->shouldReturn(array($locale));
    }

    function it_returns_correct_locales(
        RepositoryInterface $repository,
        LocaleInterface $locale1,
        LocaleInterface $locale2
    )
    {
        $locales = array($locale1, $locale2);
        $repository->findBy(Argument::any())->willReturn($locales);

        $locale1->getCode()->willReturn('en');
        $locale2->getCode()->willReturn('de');

        $this->getLocales()->shouldReturn(array('en', 'de'));
    }

    function it_returns_correct_default_locale()
    {
        $this->getDefaultLocale()->shouldReturn('en');
    }
}
