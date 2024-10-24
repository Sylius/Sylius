<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\MoneyBundle\Formatter;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\MoneyBundle\Formatter\MoneyFormatterInterface;

final class MoneyFormatterSpec extends ObjectBehavior
{
    function it_implements_money_formatter_interface(): void
    {
        $this->shouldImplement(MoneyFormatterInterface::class);
    }

    function it_formats_positive_money_using_given_currency_and_locale(): void
    {
        $this->format(15, 'USD', 'en')->shouldReturn('$0.15');
        $this->format(2500, 'USD', 'en')->shouldReturn('$25.00');
        $this->format(312, 'EUR', 'en')->shouldReturn('€3.12');
    }

    function it_formats_positive_money_using_default_locale_if_not_given(): void
    {
        $this->format(500, 'USD')->shouldReturn('$5.00');
    }

    function it_formats_negative_money_using_given_currency_and_locale(): void
    {
        $this->format(-15, 'USD', 'en')->shouldReturn('-$0.15');
        $this->format(-2500, 'USD', 'en')->shouldReturn('-$25.00');
        $this->format(-312, 'EUR', 'en')->shouldReturn('-€3.12');
    }

    function it_formats_negative_money_using_default_locale_if_not_given(): void
    {
        $this->format(-500, 'USD')->shouldReturn('-$5.00');
    }

    function it_formats_zero_money_using_given_currency_and_locale(): void
    {
        $this->format(0, 'USD', 'en')->shouldReturn('$0.00');
        $this->format(0, 'EUR', 'en')->shouldReturn('€0.00');
    }

    function it_formats_zero_money_using_default_locale_if_not_given(): void
    {
        $this->format(0, 'USD')->shouldReturn('$0.00');
    }
}
