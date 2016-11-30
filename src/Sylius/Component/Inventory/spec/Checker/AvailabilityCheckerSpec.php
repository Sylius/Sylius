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
use Sylius\Component\Inventory\Checker\AvailabilityChecker;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\StockableInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
final class AvailabilityCheckerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(AvailabilityChecker::class);
    }

    function it_is_an_inventory_availability_checker()
    {
        $this->shouldImplement(AvailabilityCheckerInterface::class);
    }

    function it_recognizes_stockable_as_available_if_on_hand_quantity_is_greater_than_0(StockableInterface $stockable)
    {
        $stockable->isTracked()->willReturn(true);
        $stockable->getOnHand()->willReturn(5);
        $stockable->getOnHold()->willReturn(0);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_not_available_if_on_hand_quantity_is_equal_to_0(StockableInterface $stockable)
    {
        $stockable->isTracked()->willReturn(true);
        $stockable->getOnHand()->willReturn(0);
        $stockable->getOnHold()->willReturn(0);

        $this->isStockAvailable($stockable)->shouldReturn(false);
    }

    function it_recognizes_stockable_as_available_if_on_hold_quantity_is_less_than_on_hand(
        StockableInterface $stockable
    ) {
        $stockable->isTracked()->willReturn(true);
        $stockable->getOnHand()->willReturn(5);
        $stockable->getOnHold()->willReturn(4);

        $this->isStockAvailable($stockable)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_not_available_if_on_hold_quantity_is_same_as_on_hand(
        StockableInterface $stockable
    ) {
        $stockable->isTracked()->willReturn(true);
        $stockable->getOnHand()->willReturn(5);
        $stockable->getOnHold()->willReturn(5);

        $this->isStockAvailable($stockable)->shouldReturn(false);
    }

    function it_recognizes_stockable_as_sufficient_if_on_hand_minus_on_hold_quantity_is_greater_than_the_required_quantity(
        StockableInterface $stockable
    ) {
        $stockable->isTracked()->willReturn(true);
        $stockable->getOnHand()->willReturn(10);
        $stockable->getOnHold()->willReturn(3);

        $this->isStockSufficient($stockable, 5)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_sufficient_if_on_hand_minus_on_hold_quantity_is_equal_to_the_required_quantity(
        StockableInterface $stockable
    ) {
        $stockable->isTracked()->willReturn(true);
        $stockable->getOnHand()->willReturn(10);
        $stockable->getOnHold()->willReturn(5);

        $this->isStockSufficient($stockable, 5)->shouldReturn(true);
    }

    function it_recognizes_stockable_as_available_or_sufficent_if_it_is_not_tracked(StockableInterface $stockable)
    {
        $stockable->isTracked()->willReturn(false);

        $this->isStockAvailable($stockable)->shouldReturn(true);
        $this->isStockSufficient($stockable, 42)->shouldReturn(true);
    }
}
