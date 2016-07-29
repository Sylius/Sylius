<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Operator;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Checker\AvailabilityCheckerInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Operator\InventoryOperator;
use Sylius\Component\Inventory\Operator\InventoryOperatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @mixin InventoryOperator
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
final class InventoryOperatorSpec extends ObjectBehavior
{
    function let(AvailabilityCheckerInterface $availabilityChecker, EventDispatcher $eventDispatcher)
    {
        $this->beConstructedWith($availabilityChecker, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InventoryOperator::class);
    }

    function it_is_an_inventory_operator()
    {
        $this->shouldImplement(InventoryOperatorInterface::class);
    }

    function it_increases_stockable_on_hand(StockableInterface $stockable)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(2);
        $stockable->setOnHand(7)->shouldBeCalled();

        $this->increase($stockable, 5);
    }

    function it_decreases_stockable_on_hand_by_count_of_sold_units(
        AvailabilityCheckerInterface $availabilityChecker,
        StockableInterface $stockable,
        InventoryUnitInterface $inventoryUnit1,
        InventoryUnitInterface $inventoryUnit2
    ) {
        $inventoryUnit1->getStockable()->willReturn($stockable);
        $inventoryUnit2->getStockable()->willReturn($stockable);

        $availabilityChecker->isStockSufficient($stockable, 2)->shouldBeCalled()->willReturn(true);

        $inventoryUnit1->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_SOLD);
        $inventoryUnit2->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_SOLD);

        $stockable->getOnHand()->shouldBeCalled()->willReturn(7);
        $stockable->setOnHand(5)->shouldBeCalled();

        $this->decrease([$inventoryUnit1, $inventoryUnit2]);
    }
}
