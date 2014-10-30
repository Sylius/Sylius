<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CurrencyBundle\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CurrencyBundle\Context\CurrencyContext;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CurrencyContextSpec extends ObjectBehavior
{
    function let(SessionInterface $session)
    {
        $this->beConstructedWith($session, 'EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Context\CurrencyContext');
    }

    function it_implements_Sylius_currency_context_interface()
    {
        $this->shouldImplement('Sylius\Component\Currency\Context\CurrencyContextInterface');
    }

    function it_gets_default_currency()
    {
        $this->getDefaultCurrency()->shouldReturn('EUR');
    }

    function it_gets_currency_from_session($session)
    {
        $session->isStarted()->shouldBeCalled()->willReturn(true);
        $session->get(CurrencyContext::SESSION_KEY, 'EUR')->shouldBeCalled()->willReturn('RSD');

        $this->getCurrency()->shouldReturn('RSD');
    }

    function it_sets_currency_to_session($session)
    {
        $session->set(CurrencyContext::SESSION_KEY, 'PLN')->shouldBeCalled();

        $this->setCurrency('PLN');
    }
}
