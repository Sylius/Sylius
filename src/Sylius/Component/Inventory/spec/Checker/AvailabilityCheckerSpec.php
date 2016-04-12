<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Checker;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class AvailabilityCheckerSpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(true);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Checker\AvailabilityChecker');
    }

    function it_implements_Sylius_inventory_availability_checker_interface()
    {
        $this->shouldImplement(AvailabilityCheckerInterface::class);
    }

    function it_recognizes_any_stockable_as_available_if_backorders_are_enabled(StockableInterface $stockable)
    {
        $this->beConstructedWith(true);

        $stockable->isAvailableOnDemand()->willReturn(false);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_any_stockable_as_available_if_its_on_demand_and_backorders_are_disabled(
        StockableInterface $stockable
    ) {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(true);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_any_stockable_as_available_if_its_on_demand_and_backorders_are_disabled_and_on_hand_quantity_insufficient(
        StockableInterface $stockable
    ) {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(true);
        $stockable->getOnHand()->willReturn(0);

        $this->isStockAvailable($stockable)->shouldReturn(true);

        $stockable->isAvailableOnDemand()->willReturn(true);
        $stockable->getOnHand()->willReturn(-5);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_available_if_on_hand_quantity_is_greater_than_0(StockableInterface $stockable)
    {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(false);
        $stockable->getOnHand()->willReturn(5);
        $stockable->getOnHold()->willReturn(0);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_not_available_if_on_hold_quantity_is_same_as_on_hand(
        StockableInterface $stockable
    ) {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(false);
        $stockable->getOnHand()->willReturn(5);
        $stockable->getOnHold()->willReturn(5);

        $this->isStockAvailable($stockable)->shouldReturn(false);
    }

    function it_recognizes_stockable_as_available_if_on_hold_quantity_is_less_then_on_hand(
        StockableInterface $stockable
    ) {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(false);
        $stockable->getOnHand()->willReturn(5);
        $stockable->getOnHold()->willReturn(4);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_available_even_if_hand_quantity_is_lesser_than_or_equal_to_0_when_backorders_are_enabled(
        StockableInterface $stockable
    ) {
        $this->beConstructedWith(true);

        $stockable->getOnHand()->willReturn(0);
        $this->isStockAvailable($stockable)->shouldReturn(true);

        $stockable->getOnHand()->willReturn(-5);
        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_not_available_if_on_hand_quantity_is_lesser_than_or_equal_to_0(
        StockableInterface $stockable
    ) {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(false);

        $stockable->getOnHand()->willReturn(0);
        $stockable->getOnHold()->willReturn(0);
        $this->isStockAvailable($stockable)->shouldReturn(false);

        $stockable->getOnHand()->willReturn(-5);
        $this->isStockAvailable($stockable)->shouldReturn(false);
    }

    function it_recognizes_any_stockable_and_quantity_as_sufficient_if_backorders_are_enabled(
        StockableInterface $stockable
    ) {
        $this->beConstructedWith(true);

        $this->isStockSufficient($stockable, 999)->shouldReturn(true);
    }

    function it_recognizes_stockable_stock_sufficient_if_on_hand_quantity_is_greater_than_required_quantity(
        StockableInterface $stockable
    ) {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(false);

        $stockable->getOnHand()->willReturn(10);
        $stockable->getOnHold()->willReturn(0);
        $this->isStockSufficient($stockable, 5)->shouldReturn(true);

        $stockable->getOnHand()->willReturn(15);
        $this->isStockSufficient($stockable, 15)->shouldReturn(true);
    }

    function it_recognizes_stock_sufficient_if_its_available_on_demand_and_backorders_are_disabled(
        StockableInterface $stockable
    ) {
        $this->beConstructedWith(false);

        $stockable->isAvailableOnDemand()->willReturn(true);

        $stockable->getOnHand()->willReturn(0);
        $this->isStockSufficient($stockable, 999)->shouldReturn(true);

        $stockable->getOnHand()->willReturn(-5);
        $this->isStockSufficient($stockable, 3)->shouldReturn(true);
    }
}
