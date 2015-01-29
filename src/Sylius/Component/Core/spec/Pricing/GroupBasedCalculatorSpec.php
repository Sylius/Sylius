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
use Sylius\Component\User\Model\GroupInterface;
use Sylius\Component\Pricing\Model\PriceableInterface;

;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class GroupBasedCalculatorSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\Pricing\GroupBasedCalculator');
    }

    function it_implements_Sylius_pricing_calculator_interface()
    {
        $this->shouldImplement('Sylius\Component\Pricing\Calculator\CalculatorInterface');
    }

    function it_returns_default_price_if_groups_are_not_in_context(PriceableInterface $priceable)
    {
        $configuration = array(
            42 => 4999,
            17 => 4599,
            95 => 4400
        );

        $priceable->getPrice()->shouldBeCalled()->willReturn(5500);
        $context = array();

        $this->calculate($priceable, $configuration, $context)->shouldReturn(5500);
    }

    function it_returns_the_default_price_if_configuration_does_not_exist_for_group(
        PriceableInterface $priceable,
        GroupInterface $group
    ) {
        $configuration = array(
            42 => 4999,
            17 => 4599,
            95 => 4400
        );

        $context = array('groups' => array($group));
        $group->getId()->shouldBeCalled()->willReturn(22);
        $priceable->getPrice()->shouldBeCalled()->willReturn(3500);

        $this->calculate($priceable, $configuration, $context)->shouldReturn(3500);
    }

    function it_returns_the_price_for_group_if_configuration_exists(
        PriceableInterface $priceable,
        GroupInterface $group
    ) {
        $configuration = array(
            42 => 4999,
            17 => 4599,
            95 => 4400
        );

        $context = array('groups' => array($group));
        $group->getId()->shouldBeCalled()->willReturn(17);

        $this->calculate($priceable, $configuration, $context)->shouldReturn(4599);
    }

    function it_returns_the_lowest_price_if_more_than_1_group_provided_in_context(
        PriceableInterface $priceable,
        GroupInterface $group1,
        GroupInterface $group2
    ) {
        $configuration = array(
            42 => 4999,
            17 => 4599,
            95 => 4400
        );

        $context = array('groups' => array($group1, $group2));
        $group1->getId()->shouldBeCalled()->willReturn(17);
        $group2->getId()->shouldBeCalled()->willReturn(95);

        $this->calculate($priceable, $configuration, $context)->shouldReturn(4400);
    }
}
