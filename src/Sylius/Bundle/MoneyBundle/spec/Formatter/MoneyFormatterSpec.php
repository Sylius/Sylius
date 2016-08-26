<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\MoneyBundle\Formatter;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatter;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;

/**
 * @mixin MoneyFormatter
 *
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
final class MoneyFormatterSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatter');
    }

    function it_implements_amount_formatter_interface()
    {
        $this->shouldImplement(MoneyFormatterInterface::class);
    }

    function it_formats_money_using_given_currency_and_locale()
    {
        $this->format(15, 'USD', 'en')->shouldReturn('$0.15');
        $this->format(2500, 'USD', 'en')->shouldReturn('$25.00');
        $this->format(312, 'EUR', 'en')->shouldReturn('€3.12');
    }

    function it_formats_money_using_default_locale_if_not_given()
    {
        $this->format(500, 'EUR')->shouldReturn('€5.00');
    }
}
