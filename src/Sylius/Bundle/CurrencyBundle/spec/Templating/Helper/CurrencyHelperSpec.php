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
use Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelperInterface;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Sylius\Component\Currency\Model\CurrencyInterface;
use Sylius\Component\Currency\Provider\CurrencyProviderInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @mixin CurrencyHelper
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class CurrencyHelperSpec extends ObjectBehavior
{
    function let(
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $converter,
        MoneyFormatterInterface $moneyFormatter,
        CurrencyProviderInterface $currencyProvider
    ) {
        $this->beConstructedWith($currencyContext, $converter, $moneyFormatter, $currencyProvider);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelper');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_implements_currency_helper_interface()
    {
        $this->shouldImplement(CurrencyHelperInterface::class);
    }

    function it_allows_to_convert_prices_in_different_currencies(
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $converter
    ) {
        $currencyContext->getCurrency()->willReturn('PLN');

        $converter->convertFromBase(15, 'USD')->willReturn(19);
        $converter->convertFromBase(2500, 'USD')->willReturn(1913);
        $converter->convertFromBase(312, 'PLN')->willReturn(407);
        $converter->convertFromBase(500, 'PLN')->willReturn(653);

        $this->convertAmount(15, 'USD')->shouldReturn(19);
        $this->convertAmount(2500, 'USD')->shouldReturn(1913);
        $this->convertAmount(312, 'PLN')->shouldReturn(407);
        $this->convertAmount(500)->shouldReturn(653);
    }

    function it_allows_to_convert_and_format_prices_in_different_currencies(
        CurrencyContextInterface $currencyContext,
        CurrencyConverterInterface $converter,
        MoneyFormatterInterface $moneyFormatter
    ) {
        $currencyContext->getCurrency()->willReturn('PLN');

        $converter->convertFromBase(15, 'USD')->willReturn(19);
        $converter->convertFromBase(2500, 'USD')->willReturn(1913);
        $converter->convertFromBase(312, 'PLN')->willReturn(407);
        $converter->convertFromBase(500, 'PLN')->willReturn(653);

        $moneyFormatter->format(19, 'USD')->willReturn('$0.19');
        $moneyFormatter->format(1913, 'USD')->willReturn('$19.13');
        $moneyFormatter->format(407, 'PLN')->willReturn('4.07 zł');
        $moneyFormatter->format(653, 'PLN')->willReturn('6.53 zł');

        $this->convertAndFormatAmount(15, 'USD')->shouldReturn('$0.19');
        $this->convertAndFormatAmount(2500, 'USD')->shouldReturn('$19.13');
        $this->convertAndFormatAmount(312, 'PLN')->shouldReturn('4.07 zł');
        $this->convertAndFormatAmount(500)->shouldReturn('6.53 zł');
    }

    function it_provides_current_currency(CurrencyProviderInterface $currencyProvider, CurrencyInterface $currency)
    {
        $currencyProvider->getBaseCurrency()->willReturn($currency);
        $currency->getCode()->willReturn('PLN');

        $this->getBaseCurrencySymbol()->shouldReturn('PLN');
    }
}
