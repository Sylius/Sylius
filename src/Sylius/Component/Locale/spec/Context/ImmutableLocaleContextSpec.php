<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Locale\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Locale\Context\ImmutableLocaleContext;
use Sylius\Component\Locale\Context\LocaleContextInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ImmutableLocaleContextSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('pl_PL');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImmutableLocaleContext::class);
    }

    function it_is_a_locale_context()
    {
        $this->shouldImplement(LocaleContextInterface::class);
    }

    function it_gets_a_locale_code()
    {
        $this->getLocaleCode()->shouldReturn('pl_PL');
    }
}
