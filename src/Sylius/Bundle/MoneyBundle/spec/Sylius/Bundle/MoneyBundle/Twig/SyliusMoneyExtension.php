<?php

namespace spec\Sylius\Bundle\MoneyBundle\Twig;

use PHPSpec2\ObjectBehavior;

/**
 * Sylius money extension for Twig spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class SyliusMoneyExtension extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\MoneyBundle\Context\CurrencyContextInterface     $currencyContext
     * @param Sylius\Bundle\MoneyBundle\Converter\CurrencyConverterInterface $converter
     */
    function let($currencyContext, $converter)
    {
        $this->beConstructedWith($currencyContext, $converter, 'en');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Twig\SyliusMoneyExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_formats_the_integer_amounts_into_string_representation($currencyContext)
    {
        $currencyContext->getCurrency()->willReturn('EUR');

        $this->formatMoney(15)->shouldReturn('€0.15');
        $this->formatMoney(2500)->shouldReturn('€25.00');
        $this->formatMoney(312)->shouldReturn('€3.12');
        $this->formatMoney(500)->shouldReturn('€5.00');
    }

    function it_allows_to_format_money_in_different_currencies($currencyContext, $converter)
    {
        $currencyContext->getCurrency()->willReturn('EUR');

        $this->formatMoney(15, 'USD')->shouldReturn('$0.15');
        $this->formatMoney(2500, 'USD')->shouldReturn('$25.00');
        $this->formatMoney(312, 'EUR')->shouldReturn('€3.12');
        $this->formatMoney(500)->shouldReturn('€5.00');
    }

    function it_allows_to_convert_prices_in_different_currencies($currencyContext, $converter)
    {
        $currencyContext->getCurrency()->willReturn('PLN');

        $converter->convert(15, 'USD')->shouldBeCalled()->willReturn(19.60);
        $converter->convert(2500, 'USD')->shouldBeCalled()->willReturn(1913.51);
        $converter->convert(312, 'PLN')->shouldBeCalled()->willReturn(407.72);
        $converter->convert(500, 'PLN')->shouldBeCalled()->willReturn(653.40);

        $this->formatPrice(15, 'USD')->shouldReturn('$0.20');
        $this->formatPrice(2500, 'USD')->shouldReturn('$19.14');
        $this->formatPrice(312, 'PLN')->shouldReturn('PLN4.08');
        $this->formatPrice(500)->shouldReturn('PLN6.53');
    }
}
