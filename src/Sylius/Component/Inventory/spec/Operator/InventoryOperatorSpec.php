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
use Sylius\Component\Inventory\Manager\InventoryManagerInterface;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Model\StockInterface;
use Sylius\Component\Inventory\Operator\BackordersHandlerInterface;
use Symfony\Component\EventDispatcher\EventDispatcher;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Saša Stamenković <umpirsky@gmail.com>
 */
class InventoryOperatorSpec extends ObjectBehavior
{
    function let(
        BackordersHandlerInterface $backordersHandler,
        InventoryManagerInterface $inventoryManager,
        EventDispatcher $eventDispatcher
    ) {
        $this->beConstructedWith($backordersHandler, $inventoryManager, $eventDispatcher);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Operator\InventoryOperator');
    }

    function it_implements_Sylius_inventory_operator_interface()
    {
        $this->shouldImplement('Sylius\Component\Inventory\Operator\InventoryOperatorInterface');
    }

    function it_increases_stockable_on_hand(StockableInterface $stockable, StockInterface $stock)
    {
        $stock->getOnHand()->shouldBeCalled()->willReturn(2);
        $stock->setOnHand(7)->shouldBeCalled();
        $stockable->getStock()->willReturn($stock);
        
        $this->increase($stockable, 5);
    }

    function it_decreases_stockable_on_hand_by_count_of_sold_units(
        InventoryManagerInterface $inventoryManager,
        $backordersHandler,
        StockableInterface $stockable,
        StockInterface $stock,
        InventoryUnitInterface $inventoryUnit1,
        InventoryUnitInterface $inventoryUnit2
    ) {
        $inventoryUnit1->getStockable()->willReturn($stockable);
        $inventoryUnit2->getStockable()->willReturn($stockable);

        $inventoryManager->isStockAvailable($stockable, 2)->shouldBeCalled()->willReturn(true);
        $backordersHandler->processBackorders(array($inventoryUnit1, $inventoryUnit2))->shouldBeCalled();

        $inventoryUnit1->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_SOLD);
        $inventoryUnit2->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_SOLD);

        $stock->getOnHand()->shouldBeCalled()->willReturn(7);
        $stock->setOnHand(5)->shouldBeCalled();
        $stockable->getStock()->willReturn($stock);

        $this->decrease(array($inventoryUnit1, $inventoryUnit2));
    }

    function it_decreases_stockable_on_hand_and_ignores_backordered_units(
        InventoryManagerInterface $inventoryManager,
        $backordersHandler,
        StockableInterface $stockable,
        StockInterface $stock,        
        InventoryUnitInterface $inventoryUnit1,
        InventoryUnitInterface $inventoryUnit2,
        InventoryUnitInterface $inventoryUnit3
    ) {
        $inventoryUnit1->getStockable()->willReturn($stockable);
        $inventoryUnit2->getStockable()->willReturn($stockable);
        $inventoryUnit3->getStockable()->willReturn($stockable);

        $inventoryManager->isStockAvailable($stockable, 3)->shouldBeCalled()->willReturn(true);
        $backordersHandler->processBackorders(array($inventoryUnit1, $inventoryUnit2, $inventoryUnit3))->shouldBeCalled();

        $inventoryUnit1->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_SOLD);
        $inventoryUnit2->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_BACKORDERED);
        $inventoryUnit3->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_BACKORDERED);

        $stock->getOnHand()->shouldBeCalled()->willReturn(1);
        $stock->setOnHand(0)->shouldBeCalled();
        $stockable->getStock()->willReturn($stock);

        $this->decrease(array($inventoryUnit1, $inventoryUnit2, $inventoryUnit3));
    }
}
