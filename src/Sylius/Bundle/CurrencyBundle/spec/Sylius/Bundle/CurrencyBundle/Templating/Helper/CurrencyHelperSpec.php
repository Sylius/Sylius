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
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Currency\Converter\CurrencyConverterInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class CurrencyHelperSpec extends ObjectBehavior
{
    function let(CurrencyContextInterface $currencyContext, CurrencyConverterInterface $converter)
    {
        $this->beConstructedWith($currencyContext, $converter, 'en');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CurrencyBundle\Templating\Helper\CurrencyHelper');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Symfony\Component\Templating\Helper\Helper');
    }

    function it_allows_to_convert_prices_in_different_currencies($currencyContext, $converter)
    {
        $currencyContext->getCurrency()->willReturn('PLN');

        $converter->convert(15, 'USD')->shouldBeCalled()->willReturn(19);
        $converter->convert(2500, 'USD')->shouldBeCalled()->willReturn(1913);
        $converter->convert(312, 'PLN')->shouldBeCalled()->willReturn(407);
        $converter->convert(500, 'PLN')->shouldBeCalled()->willReturn(653);

        $this->convertAmount(15, 'USD')->shouldReturn(19);
        $this->convertAmount(2500, 'USD')->shouldReturn(1913);
        $this->convertAmount(312, 'PLN')->shouldReturn(407);
        $this->convertAmount(500)->shouldReturn(653);
    }
}
