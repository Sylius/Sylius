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
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Converter\UnavailableCurrencyException;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

class CurrencyConverterSpec extends ObjectBehavior
{
    function let(RepositoryInterface $currencyRepository)
    {
        $this->beConstructedWith($currencyRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Currency\Converter\CurrencyConverter');
    }

    function it_implements_Sylius_currency_converter_interface()
    {
        $this->shouldImplement(CurrencyConverterInterface::class);
    }

    function it_converts_to_any_currency(CurrencyInterface $currency, $currencyRepository)
    {
        $currencyRepository->findOneBy(['code' => 'USD'])->shouldBeCalled()->willReturn($currency);
        $currency->getExchangeRate()->shouldBeCalled()->willReturn(1.30);

        $this->convertFromBase(6555, 'USD')->shouldReturn(8522);
    }

    function it_throws_exception_if_currency_is_not_found($currencyRepository)
    {
        $currencyRepository->findOneBy(['code' => 'EUR'])->shouldBeCalled()->willReturn(null);

        $this
            ->shouldThrow(new UnavailableCurrencyException('EUR'))
            ->duringConvertFromBase(6555, 'EUR')
        ;
    }

    function it_converts_to_base_currency(CurrencyInterface $currency, $currencyRepository)
    {
        $currencyRepository->findOneBy(['code' => 'PLN'])->shouldBeCalled()->willReturn($currency);
        $currency->getExchangeRate()->shouldBeCalled()->willReturn(0.25);

        $this->convertToBase(10000, 'PLN')->shouldReturn(40000);
    }
}
