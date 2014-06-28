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

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class MoneyHelperSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith('de_DE', 'EUR');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Templating\Helper\MoneyHelper');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Symfony\Component\Templating\Helper\Helper');
    }

    function it_formats_the_integer_amounts_into_string_representation($currencyContext)
    {
        $this->formatAmount(15)->shouldReturn('0,15 €');
        $this->formatAmount(2500)->shouldReturn('25,00 €');
        $this->formatAmount(312)->shouldReturn('3,12 €');
        $this->formatAmount(500)->shouldReturn('5,00 €');
    }

    function it_allows_to_format_money_in_different_currencies($currencyContext)
    {
        $this->formatAmount(15, 'USD')->shouldReturn('0,15 $');
        $this->formatAmount(2500, 'USD')->shouldReturn('25,00 $');
        $this->formatAmount(312, 'EUR')->shouldReturn('3,12 €');
        $this->formatAmount(500)->shouldReturn('5,00 €');
    }
}
