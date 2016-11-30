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
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Context\CompositeCurrencyContext;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class CompositeCurrencyContextSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(CompositeCurrencyContext::class);
    }

    function it_implements_currency_context_interface()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_throws_a_currency_not_found_exception_if_there_are_no_nested_currency_contexts_defined()
    {
        $this->shouldThrow(CurrencyNotFoundException::class)->during('getCurrencyCode');
    }

    function it_throws_a_currency_not_found_exception_if_none_of_nested_currency_contexts_returned_a_currency(
        CurrencyContextInterface $currencyContext
    ) {
        $currencyContext->getCurrencyCode()->willThrow(CurrencyNotFoundException::class);

        $this->addContext($currencyContext);

        $this->shouldThrow(CurrencyNotFoundException::class)->during('getCurrencyCode');
    }

    function it_returns_first_result_returned_by_nested_request_resolvers(
        CurrencyContextInterface $firstCurrencyContext,
        CurrencyContextInterface $secondCurrencyContext,
        CurrencyContextInterface $thirdCurrencyContext
    ) {
        $firstCurrencyContext->getCurrencyCode()->willThrow(CurrencyNotFoundException::class);
        $secondCurrencyContext->getCurrencyCode()->willReturn('BTC');
        $thirdCurrencyContext->getCurrencyCode()->shouldNotBeCalled();

        $this->addContext($firstCurrencyContext);
        $this->addContext($secondCurrencyContext);
        $this->addContext($thirdCurrencyContext);

        $this->getCurrencyCode()->shouldReturn('BTC');
    }

    function its_nested_request_resolvers_can_have_priority(
        CurrencyContextInterface $firstCurrencyContext,
        CurrencyContextInterface $secondCurrencyContext,
        CurrencyContextInterface $thirdCurrencyContext
    ) {
        $firstCurrencyContext->getCurrencyCode()->shouldNotBeCalled();
        $secondCurrencyContext->getCurrencyCode()->willReturn('BTC');
        $thirdCurrencyContext->getCurrencyCode()->willThrow(CurrencyNotFoundException::class);

        $this->addContext($firstCurrencyContext, -5);
        $this->addContext($secondCurrencyContext, 0);
        $this->addContext($thirdCurrencyContext, 5);

        $this->getCurrencyCode()->shouldReturn('BTC');
    }
}
