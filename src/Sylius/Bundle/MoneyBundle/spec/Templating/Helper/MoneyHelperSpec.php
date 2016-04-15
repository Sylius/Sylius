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
use Sylius\Bundle\MoneyBundle\Formatter\AmountFormatterInterface;
use Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelper;
use Symfony\Component\Templating\Helper\Helper;

/**
 * @mixin MoneyHelper
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Łukasz Chruściel <lukasz.chrusciel@lakion.com>
 */
class MoneyHelperSpec extends ObjectBehavior
{
    function let(AmountFormatterInterface $amountFormatter)
    {
        $this->beConstructedWith('en', 'EUR', $amountFormatter);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelper');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType(Helper::class);
    }

    function it_allows_to_format_money_in_different_currencies(AmountFormatterInterface $amountFormatter)
    {
        $amountFormatter->format(15, 'en', 'USD')->willReturn('$0.15');
        $amountFormatter->format(2500, 'en', 'USD')->willReturn('$25.00');
        $amountFormatter->format(312, 'en', 'EUR')->willReturn('€3.12');
        $amountFormatter->format(500, 'en', 'EUR')->willReturn('€5.00');

        $this->formatAmount(15, 'USD')->shouldReturn('$0.15');
        $this->formatAmount(2500, 'USD')->shouldReturn('$25.00');
        $this->formatAmount(312, 'EUR')->shouldReturn('€3.12');
        $this->formatAmount(500)->shouldReturn('€5.00');
    }
}
