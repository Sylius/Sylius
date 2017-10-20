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

namespace spec\Sylius\Component\Currency\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Model\ExchangeRateInterface;
use Sylius\Component\Currency\Repository\ExchangeRateRepositoryInterface;

final class CurrencyConverterSpec extends ObjectBehavior
{
    function let(ExchangeRateRepositoryInterface $exchangeRateRepository): void
    {
        $this->beConstructedWith($exchangeRateRepository);
    }

    function it_implements_a_currency_converter_interface(): void
    {
        $this->shouldImplement(CurrencyConverterInterface::class);
    }

    function it_converts_multipling_ratio_based_on_currency_pair_exchange_rate(
        ExchangeRateRepositoryInterface $exchangeRateRepository,
        CurrencyInterface $sourceCurrency,
        ExchangeRateInterface $exchangeRate
    ): void {
        $exchangeRateRepository->findOneWithCurrencyPair('GBP', 'USD')->willReturn($exchangeRate);
        $exchangeRate->getRatio()->willReturn(1.30);
        $exchangeRate->getSourceCurrency()->willReturn($sourceCurrency);

        $sourceCurrency->getCode()->willReturn('GBP');

        $this->convert(666, 'GBP', 'USD')->shouldReturn(866);
    }

    function it_converts_dividing_ratio_based_on_reversed_currency_pair_exchange_rate(
        ExchangeRateRepositoryInterface $exchangeRateRepository,
        CurrencyInterface $sourceCurrency,
        ExchangeRateInterface $exchangeRate
    ): void {
        $exchangeRateRepository->findOneWithCurrencyPair('GBP', 'USD')->willReturn($exchangeRate);
        $exchangeRate->getRatio()->willReturn(1.30);
        $exchangeRate->getSourceCurrency()->willReturn($sourceCurrency);

        $sourceCurrency->getCode()->willReturn('USD');

        $this->convert(666, 'GBP', 'USD')->shouldReturn(512);
    }

    function it_return_given_value_if_exchange_rate_for_given_currency_pair_has_not_been_found(
        ExchangeRateRepositoryInterface $exchangeRateRepository
    ): void {
        $exchangeRateRepository->findOneWithCurrencyPair('GBP', 'USD')->willReturn(null);

        $this->convert(666, 'GBP', 'USD')->shouldReturn(666);
    }

    function it_return_given_value_if_both_currencie_in_currency_pair_are_the_same(
        ExchangeRateRepositoryInterface $exchangeRateRepository
    ): void {
        $exchangeRateRepository->findOneWithCurrencyPair('GBP', 'GBP')->willReturn(null);

        $this->convert(666, 'GBP', 'GBP')->shouldReturn(666);
    }
}
