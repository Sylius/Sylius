<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\PricingBundle\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\PricingBundle\Model\PriceableInterface;;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class StandardCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PricingBundle\Calculator\StandardCalculator');
    }

    function it_implements_Sylius_pricing_calculator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\PricingBundle\Calculator\CalculatorInterface');
    }

    function it_returns_the_default_price_stored_on_the_priceable_object(PriceableInterface $priceable)
    {
        $priceable->getPrice()->shouldBeCalled()->willReturn(1299);
        $this->calculate($priceable, array())->shouldReturn(1299);
    }
}
