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
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelper;
use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelperInterface;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @mixin MoneyHelper
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class MoneyHelperSpec extends ObjectBehavior
{
    function let(MoneyFormatterInterface $moneyFormatter)
    {
        $this->beConstructedWith($moneyFormatter, 'USD', 'en_US');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(MoneyHelper::class);
    }

    function it_is_a_templating_helper()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_implements_money_helper_interface()
    {
        $this->shouldImplement(MoneyHelperInterface::class);
    }

    function it_formats_money_using_default_currency_and_locale_if_only_amount_is_given(
        MoneyFormatterInterface $moneyFormatter
    ) {
        $moneyFormatter->format(500, 'USD', 'en_US')->willReturn('$5.00');

        $this->formatAmount(500)->shouldReturn('$5.00');
    }

    function it_formats_money_using_default_locale_if_not_given(MoneyFormatterInterface $moneyFormatter)
    {
        $moneyFormatter->format(312, 'USD', 'en_US')->willReturn('$3.12');

        $this->formatAmount(312, 'USD')->shouldReturn('$3.12');
    }

    function it_formats_money_using_given_currency_and_locale(MoneyFormatterInterface $moneyFormatter)
    {
        $moneyFormatter->format(2500, 'EUR', 'fr_FR')->willReturn('€25.00');

        $this->formatAmount(2500, 'EUR', 'fr_FR')->shouldReturn('€25.00');
    }
}
