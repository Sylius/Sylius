<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CurrencyBundle\Twig;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CurrencyBundle\Twig\MoneyExtension;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;

class CurrencyExtensionSpec extends ObjectBehavior
{
    function let(
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $converter,
        MoneyExtension $moneyExtension
    ) {
        $this->beConstructedWith($currencyContext, $converter, $moneyExtension);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Twig\CurrencyExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_allows_to_convert_prices_in_different_currencies(
        $currencyContext,
        $converter
    ) {
        $currencyContext->getCurrency()->willReturn('PLN');

        $converter->convert(15, 'USD')->willReturn(19);
        $converter->convert(2500, 'USD')->willReturn(1913);
        $converter->convert(312, 'PLN')->willReturn(407);
        $converter->convert(500, 'PLN')->willReturn(653);

        $this->convertAmount(15, 'USD')->shouldReturn(19);
        $this->convertAmount(2500, 'USD')->shouldReturn(1913);
        $this->convertAmount(312, 'PLN')->shouldReturn(407);
        $this->convertAmount(500)->shouldReturn(653);
    }
}
