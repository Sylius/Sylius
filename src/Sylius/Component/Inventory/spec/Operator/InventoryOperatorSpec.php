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
use Sylius\Component\Inventory\Model\StockItemInterface;
use Sylius\Component\Inventory\Operator\BackordersHandlerInterface;
use Sylius\Component\Inventory\Operator\InventoryOperator;
use Sylius\Component\Inventory\Operator\InventoryOperatorInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InventoryOperatorSpec extends ObjectBehavior
{
    function let(
        BackordersHandlerInterface $backordersHandler,
        AvailabilityCheckerInterface $availabilityChecker,
        EventDispatcher $eventDispatcher
    ) {
        $this->beConstructedWith($backordersHandler, $availabilityChecker, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(InventoryOperator::class);
    }

    function it_implements_Sylius_inventory_operator_interface()
    {
        $this->shouldImplement(InventoryOperatorInterface::class);
    }

    function it_increases_stock_item_on_hand(StockItemInterface $stockItem)
    {
        $stockItem->getOnHand()->shouldBeCalled()->willReturn(2);
        $stockItem->setOnHand(7)->shouldBeCalled();

        $this->increase($stockItem, 5);
    }

    function it_decreases_stock_item_on_hand(StockItemInterface $stockItem)
    {
        $stockItem->getOnHand()->shouldBeCalled()->willReturn(5);
        $stockItem->setOnHand(3)->shouldBeCalled();

        $this->decrease($stockItem, 2);
    }
}
