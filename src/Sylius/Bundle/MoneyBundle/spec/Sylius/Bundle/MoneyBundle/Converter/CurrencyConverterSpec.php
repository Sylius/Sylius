<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Converter;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MoneyBundle\Model\ExchangeRateInterface;
use Sylius\Bundle\ResourceBundle\Model\RepositoryInterface;

class CurrencyConverterSpec extends ObjectBehavior
{
    function let(RepositoryInterface $exchangeRateRepository)
    {
        $this->beConstructedWith($exchangeRateRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Converter\CurrencyConverter');
    }

    function it_implements_Sylius_exchange_rate_interface()
    {
        $this->shouldImplement('Sylius\Bundle\MoneyBundle\Converter\CurrencyConverterInterface');
    }

    function it_converts_to_any_currency(ExchangeRateInterface $exchangeRate, $exchangeRateRepository)
    {
        $exchangeRateRepository->findOneBy(array('currency' => 'USD'))->shouldBeCalled()->willReturn($exchangeRate);
        $exchangeRate->getRate()->shouldBeCalled()->willReturn(1.30);

        $this->convert(65.55, 'USD')->shouldReturnFloat(85.215);
    }

    public function getMatchers()
    {
        return array(
            'returnFloat' => function ($a, $b) {
                return (string) $a === (string) $b;
            }
        );
    }
}
