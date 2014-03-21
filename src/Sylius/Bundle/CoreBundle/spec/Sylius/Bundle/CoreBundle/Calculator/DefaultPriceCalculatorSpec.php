<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\PriceableInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class DefaultPriceCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Calculator\DefaultPriceCalculator');
    }

    function it_implements_Sylius_price_calculator_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\Calculator\PriceCalculatorInterface');
    }

    function it_returns_priceable_price(PriceableInterface $priceable)
    {
        $priceable->getPrice()->shouldBeCalled()->willReturn(27);

        $this->calculate($priceable)->shouldReturn(27);
    }
}
