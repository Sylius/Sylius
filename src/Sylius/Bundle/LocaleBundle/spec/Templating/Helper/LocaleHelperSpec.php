<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\LocaleBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper;
use Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelperInterface;
use Sylius\Component\Locale\Context\LocaleContextInterface;
use Sylius\Component\Locale\Converter\LocaleConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 */
final class LocaleHelperSpec extends ObjectBehavior
{
    function let(LocaleConverterInterface $localeConverter)
    {
        $this->beConstructedWith($localeConverter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LocaleHelper::class);
    }

    function it_is_a_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_is_a_locale_helper()
    {
        $this->shouldImplement(LocaleHelperInterface::class);
    }

    function it_converts_locales_code_to_name(LocaleConverterInterface $localeConverter)
    {
        $localeConverter->convertCodeToName('fr_FR')->willReturn('French (France)');

        $this->convertCodeToName('fr_FR')->shouldReturn('French (France)');
    }

    function it_has_a_name()
    {
        $this->getName()->shouldReturn('sylius_locale');
    }
}
