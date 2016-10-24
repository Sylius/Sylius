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
use Sylius\Component\Currency\Context\CurrencyNotFoundException;
use Sylius\Component\Currency\Context\ProviderBasedCurrencyContext;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;

/**
 * @author Kamil Kokot <kamil.kokot@lakion.com>
 */
final class ProviderBasedCurrencyContextSpec extends ObjectBehavior
{
    function let(CurrencyProviderInterface $currencyProvider)
    {
        $this->beConstructedWith($currencyProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ProviderBasedCurrencyContext::class);
    }

    function it_is_a_currency_context()
    {
        $this->shouldImplement(CurrencyContextInterface::class);
    }

    function it_returns_the_channels_default_currency(CurrencyProviderInterface $currencyProvider)
    {
        $currencyProvider->getAvailableCurrenciesCodes()->willReturn(['EUR', 'PLN']);
        $currencyProvider->getDefaultCurrencyCode()->willReturn('EUR');

        $this->getCurrencyCode()->shouldReturn('EUR');
    }

    function it_throws_a_currency_not_found_exception_if_default_currency_is_not_available(
        CurrencyProviderInterface $currencyProvider
    ) {
        $currencyProvider->getAvailableCurrenciesCodes()->willReturn(['GBP', 'PLN']);
        $currencyProvider->getDefaultCurrencyCode()->willReturn('EUR');

        $this->shouldThrow(CurrencyNotFoundException::class)->during('getCurrencyCode');
    }
}
