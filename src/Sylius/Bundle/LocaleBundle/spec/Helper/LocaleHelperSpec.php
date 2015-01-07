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
use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class LocaleHelperSpec extends ObjectBehavior
{
    function let(LocaleContextInterface $localeContext)
    {
        $this->beConstructedWith($localeContext);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\LocaleBundle\Templating\Helper\LocaleHelper');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Symfony\Component\Templating\Helper\Helper');
    }

    function it_returns_the_currently_selected_locale_via_context($localeContext)
    {
        $localeContext->getLocale()->willReturn('fr_FR');

        $this->getLocale()->shouldReturn('fr_FR');
    }
}
