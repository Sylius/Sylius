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
     * @param Sylius\Bundle\MoneyBundle\Converter\CurrencyConverterInterface $converter
     */
    function let($converter)
    {
        $this->beConstructedWith($converter, 'EUR', 'en');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\MoneyBundle\Twig\SyliusMoneyExtension');
    }

    function it_is_a_Twig_extension()
    {
        $this->shouldHaveType('Twig_Extension');
    }

    function it_formats_the_integer_amounts_into_string_representation()
    {
        $this->formatMoney(15)->shouldReturn('€0.15');
        $this->formatMoney(2500)->shouldReturn('€25.00');
        $this->formatMoney(312)->shouldReturn('€3.12');
        $this->formatMoney(500)->shouldReturn('€5.00');
    }

    function it_allows_to_format_amounts_in_different_currencies($converter)
    {
        $converter->convert(15, 'USD')->shouldBeCalled()->willReturn(19.60);
        $converter->convert(2500, 'USD')->shouldBeCalled()->willReturn(1913.51);
        $converter->convert(312, 'USD')->shouldBeCalled()->willReturn(407.72);
        $converter->convert(500, 'USD')->shouldBeCalled()->willReturn(653.40);

        $this->formatMoney(15, 'USD')->shouldReturn('$0.20');
        $this->formatMoney(2500, 'USD')->shouldReturn('$19.14');
        $this->formatMoney(312, 'USD')->shouldReturn('$4.08');
        $this->formatMoney(500, 'USD')->shouldReturn('$6.53');
    }
}
