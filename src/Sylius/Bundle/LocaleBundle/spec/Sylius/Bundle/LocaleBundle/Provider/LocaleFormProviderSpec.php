<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\LocaleBundle\Provider;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Resource\Repository\RepositoryInterface;
use Sylius\Component\Locale\Model\LocaleInterface;

/**
 * @author Gonzalo Vilaseca <gvilaseca@reiss.co.uk>
 */
class LocaleFormProviderSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository, 'en');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\LocaleBundle\Provider\LocaleFormProvider');
    }

    function it_validates_a_null_default_locale_is_given(RepositoryInterface $repository)
    {
        $this->shouldThrow('Exception')->during('__construct', [$repository, null]);
    }

    function it_validates_an_empty_default_locale_is_given(RepositoryInterface $repository)
    {
        $this->shouldThrow('Exception')->during('__construct', [$repository, '']);
    }

    function it_implements_A2lix_locale_provider_interface()
    {
        $this->shouldImplement('A2lix\TranslationFormBundle\Locale\LocaleProviderInterface');
    }

    function it_has_no_required_locales()
    {
        $this->getRequiredLocales()->shouldReturn(array());
    }

    function it_returns_correct_locales_if_none_defined(RepositoryInterface $repository)
    {
        $repository->findBy(Argument::any())->willReturn(array());

        $this->getLocales()->shouldReturn(array('en'));
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
