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
use Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelper;
use Sylius\Bundle\MoneyBundle\Templating\Helper\ConvertMoneyHelperInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class ConvertMoneyHelperSpec extends ObjectBehavior
{
    function let(CurrencyConverterInterface $currencyConverter)
    {
        $this->beConstructedWith($currencyConverter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(ConvertMoneyHelper::class);
    }

    function it_is_a_templating_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_is_a_convert_money_price_helper()
    {
        $this->shouldImplement(ConvertMoneyHelperInterface::class);
    }

    function it_converts_and_formats_money_using_default_locale_if_not_given(
        CurrencyConverterInterface $currencyConverter
    ) {
        $currencyConverter->convert(500, 'USD', 'CAD')->willReturn(250);

        $this->convertAmount(500, 'USD', 'CAD')->shouldReturn(250);
    }
}
