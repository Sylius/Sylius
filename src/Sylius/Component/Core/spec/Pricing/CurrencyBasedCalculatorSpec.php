<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Pricing;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Currency\Context\CurrencyContextInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Alexandre Bacco <alexandre.bacco@gmail.com>
 */
class CurrencyBasedCalculatorSpec extends ObjectBehavior
{
    function let(CurrencyContextInterface $currencyContext)
    {
        $this->beConstructedWith($currencyContext);
    }
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Pricing\CurrencyBasedCalculator');
    }

    function it_implements_sylius_pricing_calculator_interface()
    {
        $this->shouldImplement('Sylius\Component\Pricing\Calculator\CalculatorInterface');
    }

    function it_returns_default_price_if_currency_is_not_in_configuration(PriceableInterface $priceable, $currencyContext)
    {
        $configuration = array(
            'SGD' => 49.99,
            'USD' => 45.99
        );

        $currencyContext->getCurrency()->willReturn('EUR');

        $priceable->getPrice()->shouldBeCalled()->willReturn(5500);

        $this->calculate($priceable, $configuration)->shouldReturn(5500);
    }

    function it_returns_the_price_for_currency_if_configuration_exists(PriceableInterface $priceable, $currencyContext)
    {
        $configuration = array(
            'EUR' => 55.99,
            'SGD' => 49.99,
            'USD' => 45.99
        );

        $currencyContext->getCurrency()->willReturn('SGD');

        $this->calculate($priceable, $configuration)->shouldReturn(4999);
    }
}
