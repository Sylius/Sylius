<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\LocaleBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Context\LocaleContextInterface;

class LocaleExtensionSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext)
    {
        $this->beConstructedWith($localeContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\LocaleBundle\Twig\LocaleExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_has_locale($localeContext)
    {
        $localeContext->getCurrentLocale()->willReturn('fr_FR');

        $this->getCurrentLocale()->shouldReturn('fr_FR');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_locale');
    }
}
