<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Currency\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Converter\CurrencyConverter;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Converter\UnavailableCurrencyException;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

final class CurrencyConverterSpec extends ObjectBehavior
{
    function let(RepositoryInterface $currencyRepository)
    {
        $this->beConstructedWith($currencyRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(CurrencyConverter::class);
    }

    function it_implements_a_currency_converter_interface()
    {
        $this->shouldImplement(CurrencyConverterInterface::class);
    }

    function it_converts_to_any_currency(
        RepositoryInterface $currencyRepository,
        CurrencyInterface $currency
    ) {
        $currencyRepository->findOneBy(['code' => 'USD'])->willReturn($currency);
        $currency->getExchangeRate()->willReturn(1.30);

        $this->convertFromBase(6555, 'USD')->shouldReturn(8522);
    }

    function it_throws_exception_if_currency_is_not_found($currencyRepository)
    {
        $currencyRepository->findOneBy(['code' => 'EUR'])->willReturn(null);

        $this
            ->shouldThrow(new UnavailableCurrencyException('EUR'))
            ->duringConvertFromBase(6555, 'EUR')
        ;
    }

    function it_converts_to_base_currency(
        RepositoryInterface $currencyRepository,
        CurrencyInterface $currency
    ) {
        $currencyRepository->findOneBy(['code' => 'PLN'])->willReturn($currency);
        $currency->getExchangeRate()->willReturn(0.25);

        $this->convertToBase(10000, 'PLN')->shouldReturn(40000);
    }
}
