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
use Prophecy\Argument;
use SM\Factory\FactoryInterface;
use SM\StateMachine\StateMachineInterface;
use Sylius\Component\Inventory\InventoryUnitTransitions;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Operator\BackordersHandler;
use Sylius\Component\Inventory\Operator\BackordersHandlerInterface;
use Sylius\Component\Inventory\Repository\InventoryUnitRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 * @author Robin Jansen <robinjansen51@gmail.com>
 */
class BackordersHandlerSpec extends ObjectBehavior
{
    function let(
        InventoryUnitRepositoryInterface $repository,
        FactoryInterface $factory
    ) {
        $this->beConstructedWith($repository, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(BackordersHandler::class);
    }

    function it_implements_Sylius_inventory_backorders_handler_interface()
    {
        $this->shouldImplement(BackordersHandlerInterface::class);
    }

    function it_backorders_units_if_quantity_is_greater_than_on_hand(
        StockableInterface $stockable,
        InventoryUnitInterface $inventoryUnit1,
        InventoryUnitInterface $inventoryUnit2,
        InventoryUnitInterface $inventoryUnit3
    ) {
        $inventoryUnit1->getStockable()->shouldBeCalled()->willReturn($stockable);
        $inventoryUnit2->getStockable()->shouldBeCalled()->willReturn($stockable);
        $inventoryUnit3->getStockable()->shouldBeCalled()->willReturn($stockable);

        $stockable->getOnHand()->willReturn(2);

        $inventoryUnit1->setInventoryState(Argument::any())->shouldNotBeCalled();
        $inventoryUnit2->setInventoryState(Argument::any())->shouldNotBeCalled();
        $inventoryUnit3->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED)->shouldBeCalled();

        $this->processBackorders([$inventoryUnit1, $inventoryUnit2, $inventoryUnit3]);
    }

    function it_complains_if_inventory_units_contain_different_stockables(
        StockableInterface $stockable1,
        StockableInterface $stockable2,
        InventoryUnitInterface $inventoryUnit1,
        InventoryUnitInterface $inventoryUnit2
    ) {
        $inventoryUnit1->getStockable()->shouldBeCalled()->willReturn($stockable1);
        $inventoryUnit2->getStockable()->shouldBeCalled()->willReturn($stockable2);

        $stockable1->getOnHand()->shouldBeCalled()->willReturn(50);

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->duringProcessBackorders([$inventoryUnit1, $inventoryUnit2])
        ;
    }

    function it_partially_fills_backordered_units(
        $repository,
        $factory,
        StateMachineInterface $stateMachine,
        StockableInterface $stockable,
        InventoryUnitInterface $inventoryUnit1
    ) {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(1);
        $stockable->setOnHand(0)->shouldBeCalled();

        $repository
            ->findByStockableAndInventoryState(
                $stockable,
                InventoryUnitInterface::STATE_BACKORDERED,
                1
            )
            ->shouldBeCalled()
            ->willReturn([$inventoryUnit1])
        ;

        $factory->get($inventoryUnit1, InventoryUnitTransitions::GRAPH)
            ->shouldBeCalled()
            ->willReturn($stateMachine)
        ;

        $stateMachine->apply(InventoryUnitTransitions::SYLIUS_SELL)
            ->shouldBeCalledTimes(1)
            ->willReturn(true)
        ;

        $this->fillBackorders($stockable);
    }

    function it_fills_backordered_units(
        $repository,
        $factory,
        StateMachineInterface $stateMachine,
        StockableInterface $stockable,
        InventoryUnitInterface $inventoryUnit1,
        InventoryUnitInterface $inventoryUnit2
    ) {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(5);
        $stockable->setOnHand(3)->shouldBeCalled();

        $repository
            ->findByStockableAndInventoryState(
                $stockable,
                InventoryUnitInterface::STATE_BACKORDERED,
                5
            )
            ->shouldBeCalled()
            ->willReturn([$inventoryUnit1, $inventoryUnit2])
        ;

        $factory->get($inventoryUnit1, InventoryUnitTransitions::GRAPH)
            ->willReturn($stateMachine)
        ;

        $factory->get($inventoryUnit2, InventoryUnitTransitions::GRAPH)
            ->willReturn($stateMachine)
        ;

        $stateMachine->apply(InventoryUnitTransitions::SYLIUS_SELL)
            ->shouldBeCalledTimes(2)
            ->willReturn(true)
        ;

        $this->fillBackorders($stockable);
    }
}
