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
    function let()
    {
        $this->beConstructedWith('EUR');
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
        $this->formatMoney(15)->shouldReturn('0,15 €');
        $this->formatMoney(2500)->shouldReturn('25,00 €');
        $this->formatMoney(312)->shouldReturn('3,12 €');
        $this->formatMoney(500)->shouldReturn('5,00 €');
    }

    function it_allows_to_format_amounts_in_different_currencies()
    {
        $this->formatMoney(15, 'USD')->shouldReturn('0,15 US$');
        $this->formatMoney(2500, 'USD')->shouldReturn('25,00 US$');
        $this->formatMoney(312, 'USD')->shouldReturn('3,12 US$');
        $this->formatMoney(500, 'USD')->shouldReturn('5,00 US$');
    }
}
