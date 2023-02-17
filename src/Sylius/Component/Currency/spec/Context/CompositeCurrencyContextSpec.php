<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Currency\Context;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Context\CurrencyNotFoundException;

final class CompositeCurrencyContextSpec extends ObjectBehavior
{
    function it_implements_currency_context_interface(): void
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_throws_a_currency_not_found_exception_if_there_are_no_nested_currency_contexts_defined(): void
    {
        $this->shouldThrow(CurrencyNotFoundException::class)->during('getCurrencyCode');
    }

    function it_throws_a_currency_not_found_exception_if_none_of_nested_currency_contexts_returned_a_currency(
        CurrencyContextInterface $currencyContext,
    ): void {
        $currencyContext->getCurrencyCode()->willThrow(CurrencyNotFoundException::class);

        $this->beConstructedWith([$currencyContext]);

        $this->shouldThrow(CurrencyNotFoundException::class)->during('getCurrencyCode');
    }

    function it_returns_first_result_returned_by_nested_request_resolvers(
        CurrencyContextInterface $firstCurrencyContext,
        CurrencyContextInterface $secondCurrencyContext,
        CurrencyContextInterface $thirdCurrencyContext,
    ): void {
        $firstCurrencyContext->getCurrencyCode()->willThrow(CurrencyNotFoundException::class);
        $secondCurrencyContext->getCurrencyCode()->willReturn('BTC');
        $thirdCurrencyContext->getCurrencyCode()->shouldNotBeCalled();

        $this->beConstructedWith([$firstCurrencyContext, $secondCurrencyContext, $thirdCurrencyContext]);

        $this->getCurrencyCode()->shouldReturn('BTC');
    }
}
