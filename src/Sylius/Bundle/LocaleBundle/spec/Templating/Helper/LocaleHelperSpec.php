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
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
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

    function it_is_a_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_has_locale($localeContext)
    {
        $localeContext->getCurrentLocale()->shouldBeCalled()->willReturn('fr_FR');

        $this->getCurrentLocale()->shouldReturn('fr_FR');
    }

    function it_has_name()
    {
        $this->getName()->shouldReturn('sylius_locale');
    }
}
