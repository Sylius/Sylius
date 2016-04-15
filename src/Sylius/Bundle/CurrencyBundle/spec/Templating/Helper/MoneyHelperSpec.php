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
use Sylius\Bundle\CurrencyBundle\Templating\Helper\MoneyHelper;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;
use Sylius\Component\Currency\Context\CurrencyContextInterface;

/**
 * @mixin MoneyHelper
 *
 * @author Arnaud Langlade <arn0d.dev@gmail.com>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class MoneyHelperSpec extends ObjectBehavior
{
    function let(CurrencyContextInterface $currencyContext, MoneyFormatterInterface $moneyFormatter)
    {
        $this->beConstructedWith('fr_FR', $currencyContext, $moneyFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Templating\Helper\MoneyHelper');
    }

    function it_implements_money_helper_interface()
    {
        $this->shouldImplement(MoneyHelperInterface::class);
    }

    function it_formats_money_using_default_currency_and_locale_if_only_amount_is_given(
        CurrencyContextInterface $currencyContext, 
        MoneyFormatterInterface $moneyFormatter
    ) {
        $currencyContext->getCurrency()->willReturn('EUR');
        $moneyFormatter->format(500, 'EUR', 'fr_FR')->willReturn('€5.00');

        $this->formatAmount(500)->shouldReturn('€5.00');
    }

    function it_formats_money_using_default_locale_if_not_given(MoneyFormatterInterface $moneyFormatter)
    {
        $moneyFormatter->format(312, 'EUR', 'fr_FR')->willReturn('€3.12');

        $this->formatAmount(312, 'EUR')->shouldReturn('€3.12');
    }

    function it_formats_money_using_given_currency_and_locale(MoneyFormatterInterface $moneyFormatter)
    {
        $moneyFormatter->format(2500, 'USD', 'en_US')->willReturn('$25.00');

        $this->formatAmount(2500, 'USD', 'en_US')->shouldReturn('$25.00');
    }
}
