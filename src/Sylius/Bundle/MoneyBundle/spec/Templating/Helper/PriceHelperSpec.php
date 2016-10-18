<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Templating\Helper;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\PriceHelper;
use Sylius\Bundle\MoneyBundle\Templating\Helper\PriceHelperInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class PriceHelperSpec extends ObjectBehavior
{
    function let(CurrencyConverterInterface $currencyConverter, MoneyHelperInterface $moneyHelper)
    {
        $this->beConstructedWith($currencyConverter, $moneyHelper);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PriceHelper::class);
    }

    function it_is_a_templating_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_is_a_price_helper()
    {
        $this->shouldImplement(PriceHelperInterface::class);
    }

    function it_formats_money_using_default_locale_if_only_amount_is_given(
        CurrencyConverterInterface $currencyConverter,
        MoneyHelperInterface $moneyHelper
    ) {
        $currencyConverter->convertFromBase(Argument::cetera())->shouldNotBeCalled();

        $moneyHelper->formatAmount(500, null, null)->willReturn('€5.00');

        $this->convertAndFormatAmount(500)->shouldReturn('€5.00');
    }

    function it_converts_and_formats_money_using_default_locale_if_not_given(
        CurrencyConverterInterface $currencyConverter,
        MoneyHelperInterface $moneyHelper
    ) {
        $currencyConverter->convertFromBase(500, 'USD')->willReturn(250);

        $moneyHelper->formatAmount(250, 'USD', null)->willReturn('$2.50');

        $this->convertAndFormatAmount(500, 'USD')->shouldReturn('$2.50');
    }

    function it_converts_money_using_given_currency_and_locale(
        CurrencyConverterInterface $currencyConverter,
        MoneyHelperInterface $moneyHelper
    ) {
        $currencyConverter->convertFromBase(500, 'USD')->willReturn(250);

        $moneyHelper->formatAmount(250, 'USD', 'fr_FR')->willReturn('$2.50');

        $this->convertAndFormatAmount(500, 'USD', null, 'fr_FR')->shouldReturn('$2.50');
    }

    function it_converts_money_using_given_currency_and_exchange_rate(
        CurrencyConverterInterface $currencyConverter,
        MoneyHelperInterface $moneyHelper
    ) {
        $currencyConverter->convertFromBase(Argument::cetera())->shouldNotBeCalled();

        $moneyHelper->formatAmount(300, 'GBP', null)->willReturn('£3.00');

        $this->convertAndFormatAmount(100, 'GBP', 3.0)->shouldReturn('£3.00');
    }
}
