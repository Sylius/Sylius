<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CurrencyBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelper;
use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @mixin CurrencyHelper
 * 
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CurrencyHelperSpec extends ObjectBehavior
{
    function let(
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $converter,
        MoneyHelperInterface $moneyHelper,
        CurrencyProviderInterface $currencyProvider
    ) {
        $this->beConstructedWith($currencyContext, $converter, $moneyHelper, $currencyProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelper');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_allows_to_convert_prices_in_different_currencies(
        $currencyContext,
        $converter
    ) {
        $currencyContext->getCurrency()->willReturn('PLN');

        $converter->convertFromBase(15, 'USD')->shouldBeCalled()->willReturn(19);
        $converter->convertFromBase(2500, 'USD')->shouldBeCalled()->willReturn(1913);
        $converter->convertFromBase(312, 'PLN')->shouldBeCalled()->willReturn(407);
        $converter->convertFromBase(500, 'PLN')->shouldBeCalled()->willReturn(653);

        $this->convertAmount(15, 'USD')->shouldReturn(19);
        $this->convertAmount(2500, 'USD')->shouldReturn(1913);
        $this->convertAmount(312, 'PLN')->shouldReturn(407);
        $this->convertAmount(500)->shouldReturn(653);
    }

    function it_provides_current_currency(CurrencyProviderInterface $currencyProvider)
    {
        $currencyProvider->getBaseCurrency()->willReturn('PLN');

        $this->getBaseCurrencySymbol()->shouldReturn('PLN');
    }
}
