<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Currency\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\ImmutableCurrencyContext;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ImmutableCurrencyContextSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ImmutableCurrencyContext::class);
    }

    function it_is_a_currency_context()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_gets_a_currency()
    {
        $this->getCurrencyCode()->shouldReturn('EUR');
    }
}
