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
class VolumeBasedCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Pricing\Calculator\VolumeBasedCalculator');
    }

    function it_implements_Sylius_pricing_calculator_interface()
    {
        $this->shouldImplement(CalculatorInterface::class);
    }

    function it_assumes_quantity_is_1_if_not_provided_in_context(PriceableInterface $priceable)
    {
        $configuration = [
            ['min' => 0,  'max' => 9,  'price' => 1699],
            ['min' => 10, 'max' => 19, 'price' => 1499],
            ['min' => 20, 'max' => 29, 'price' => 1299],
        ];

        $this->calculate($priceable, $configuration)->shouldReturn(1699);
    }

    function it_returns_the_price_based_on_the_quantity(PriceableInterface $priceable)
    {
        $configuration = [
            ['min' => 0,  'max' => 9,  'price' => 1699],
            ['min' => 10, 'max' => 19, 'price' => 1499],
            ['min' => 20, 'max' => 29, 'price' => 1299],
        ];

        $this->calculate($priceable, $configuration, ['quantity' => 15])->shouldReturn(1499);
        $this->calculate($priceable, $configuration, ['quantity' => 5])->shouldReturn(1699);
    }

    function it_returns_the_correct_price_for_highest_quantity_range(PriceableInterface $priceable)
    {
        $configuration = [
            ['min' => 0,  'max' => 9,    'price' => 1699],
            ['min' => 10, 'max' => 19,   'price' => 1499],
            ['min' => 20, 'max' => 29,   'price' => 1299],
            ['min' => 30, 'max' => null, 'price' => 1099],
        ];

        $this->calculate($priceable, $configuration, ['quantity' => 15])->shouldReturn(1499);
        $this->calculate($priceable, $configuration, ['quantity' => 600])->shouldReturn(1099);
    }

    function it_has_valid_type()
    {
        $this->getType()->shouldReturn(Calculators::VOLUME_BASED);
    }
}
