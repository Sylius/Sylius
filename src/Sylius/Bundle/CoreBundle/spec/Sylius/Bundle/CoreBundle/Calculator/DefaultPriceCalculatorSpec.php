<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Calculator;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Model\VariantInterface;

/**
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class DefaultPriceCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Calculator\DefaultPriceCalculator');
    }

    function it_implements_Sylius_price_calculator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\Calculator\PriceCalculatorInterface');
    }

    function it_returns_variant_price(VariantInterface $variant)
    {
        $variant->getPrice()->shouldBeCalled()->willReturn(27);

        $this->calculate($variant)->shouldReturn(27);
    }
}
