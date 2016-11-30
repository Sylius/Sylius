<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Taxation\Strategy;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\CoreBundle\Taxation\Strategy\TaxCalculationStrategy;
use Sylius\Component\Addressing\Model\ZoneInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Taxation\Applicator\OrderTaxesApplicatorInterface;
use Sylius\Component\Core\Taxation\Strategy\TaxCalculationStrategyInterface;

/**
 * @author Mark McKelvie <mark.mckelvie@reiss.com>
 */
final class TaxCalculationStrategySpec extends ObjectBehavior
{
    function let(
        OrderTaxesApplicatorInterface $applicatorOne,
        OrderTaxesApplicatorInterface $applicatorTwo
    ) {
        $this->beConstructedWith('order_items_based', [$applicatorOne, $applicatorTwo]);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(TaxCalculationStrategy::class);
    }

    function it_implements_a_tax_calculation_strategy_interface()
    {
        $this->shouldImplement(TaxCalculationStrategyInterface::class);
    }

    function it_has_a_type()
    {
        $this->getType()->shouldReturn('order_items_based');
    }

    function it_throws_an_exception_if_any_of_the_applicators_are_not_of_the_correct_type(
        OrderTaxesApplicatorInterface $applicatorOne,
        OrderTaxesApplicatorInterface $applicatorTwo,
        \stdClass $applicatorThree
    ) {
        $this->beConstructedWith('order_items_based', [$applicatorOne, $applicatorTwo, $applicatorThree]);

        $this->shouldThrow(\InvalidArgumentException::class)->duringInstantiation();
    }

    function it_can_be_supported_when_the_tax_calculation_strategy_from_order_channel_matches_the_strategy_type(
        ChannelInterface $channel,
        OrderInterface $order,
        ZoneInterface $zone
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getTaxCalculationStrategy()->willReturn('order_items_based');

        $this->supports($order, $zone)->shouldReturn(true);
    }

    function it_cannot_be_supported_when_the_tax_calculation_strategy_from_order_channel_does_not_match_the_strategy_type(
        ChannelInterface $channel,
        OrderInterface $order,
        ZoneInterface $zone
    ) {
        $order->getChannel()->willReturn($channel);
        $channel->getTaxCalculationStrategy()->willReturn('order_item_units_based');

        $this->supports($order, $zone)->shouldReturn(false);
    }

    function it_applies_all_of_the_applicators(
        OrderTaxesApplicatorInterface $applicatorOne,
        OrderTaxesApplicatorInterface $applicatorTwo,
        OrderInterface $order,
        ZoneInterface $zone
    ) {
        $applicatorOne->apply($order, $zone)->shouldBeCalled();
        $applicatorTwo->apply($order, $zone)->shouldBeCalled();

        $this->applyTaxes($order, $zone);
    }
}
