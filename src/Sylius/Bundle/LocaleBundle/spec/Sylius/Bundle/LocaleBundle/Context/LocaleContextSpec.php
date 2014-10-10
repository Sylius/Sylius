<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\LocaleBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\LocaleBundle\Context\LocaleContext;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class LocaleContextSpec extends ObjectBehavior
{
    function let(SessionInterface $session)
    {
        $this->beConstructedWith($session, 'pl_PL');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\LocaleBundle\Context\LocaleContext');
    }

    function it_implements_Sylius_locale_context_interface()
    {
        $this->shouldImplement('Sylius\Component\Locale\Context\LocaleContextInterface');
    }

    function it_gets_default_locale()
    {
        $this->getDefaultLocale()->shouldReturn('pl_PL');
    }

    function it_gets_locale_from_session($session)
    {
        $session->get(LocaleContext::SESSION_KEY, 'pl_PL')->shouldBeCalled()->willReturn('en_US');

        $this->getLocale()->shouldReturn('en_US');
    }

    function it_sets_locale_to_session($session)
    {
        $session->set(LocaleContext::SESSION_KEY, 'en_GB')->shouldBeCalled();

        $this->setLocale('en_GB');
    }
}
