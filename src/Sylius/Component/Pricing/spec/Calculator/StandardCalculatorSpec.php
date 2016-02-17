<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Pricing\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Pricing\Calculator\CalculatorInterface;
use Sylius\Component\Pricing\Calculator\Calculators;
use Sylius\Component\Pricing\Model\PriceableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StandardCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Pricing\Calculator\StandardCalculator');
    }

    function it_implements_Sylius_pricing_calculator_interface()
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_returns_the_default_price_stored_on_the_priceable_object(PriceableInterface $priceable)
    {
        $priceable->getPrice()->willReturn(1299);

        $this->calculate($priceable, [])->shouldReturn(1299);
    }

    function it_has_valid_type()
    {
        $this->getType()->shouldReturn(Calculators::STANDARD);
    }
}
