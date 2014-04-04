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
class VolumeBasedCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\PricingBundle\Calculator\VolumeBasedCalculator');
    }

    function it_implements_Sylius_pricing_calculator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\PricingBundle\Calculator\CalculatorInterface');
    }

    function it_assumes_quantity_is_1_if_not_provided_in_context(PriceableInterface $priceable)
    {
        $configuration = array(
            array('min' => 0,  'max' => 9,  'price' => 1699),
            array('min' => 10, 'max' => 19, 'price' => 1499),
            array('min' => 20, 'max' => 29, 'price' => 1299),
        );

        $this->calculate($priceable, $configuration)->shouldReturn(1699);
    }

    function it_returns_the_price_based_on_the_quantity(PriceableInterface $priceable)
    {
        $configuration = array(
            array('min' => 0,  'max' => 9,  'price' => 1699),
            array('min' => 10, 'max' => 19, 'price' => 1499),
            array('min' => 20, 'max' => 29, 'price' => 1299),
        );

        $this->calculate($priceable, $configuration, array('quantity' => 15))->shouldReturn(1499);
        $this->calculate($priceable, $configuration, array('quantity' => 5))->shouldReturn(1699);
    }

    function it_returns_the_correct_price_for_highest_quantity_range(PriceableInterface $priceable)
    {
        $configuration = array(
            array('min' => 0,  'max' => 9,    'price' => 1699),
            array('min' => 10, 'max' => 19,   'price' => 1499),
            array('min' => 20, 'max' => 29,   'price' => 1299),
            array('min' => 30, 'max' => null, 'price' => 1099),
        );

        $this->calculate($priceable, $configuration, array('quantity' => 15))->shouldReturn(1499);
        $this->calculate($priceable, $configuration, array('quantity' => 600))->shouldReturn(1099);
    }

    function it_has_valid_configuration_form_type()
    {
        $this->getConfigurationFormType()->shouldReturn('sylius_price_calculator_volume_based');
    }
}
