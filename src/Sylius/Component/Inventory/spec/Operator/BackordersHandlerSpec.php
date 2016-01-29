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

use Doctrine\Common\Persistence\ObjectRepository;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Inventory\Operator\BackordersHandlerInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class BackordersHandlerSpec extends ObjectBehavior
{
    function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Operator\BackordersHandler');
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

    function it_partially_fills_backordered_units_if_not_enough_in_stock(
        StockableInterface $stockable,
        InventoryUnitInterface $inventoryUnit1,
        InventoryUnitInterface $inventoryUnit2,
        ObjectRepository $repository
    ) {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(1);
        $stockable->setOnHand(0)->shouldBeCalled();

        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldNotBeCalled();

        $repository
            ->findBy(
                [
                    'stockable' => $stockable,
                    'inventoryState' => InventoryUnitInterface::STATE_BACKORDERED,
                ],
                [
                    'createdAt' => 'ASC',
                ]
            )
            ->willReturn([$inventoryUnit1, $inventoryUnit2])
        ;

        $this->fillBackorders($stockable);
    }

    function it_fills_all_backordered_units_if_enough_in_stock(
        StockableInterface $stockable,
        InventoryUnitInterface $inventoryUnit1,
        InventoryUnitInterface $inventoryUnit2,
        InventoryUnitInterface $inventoryUnit3,
        ObjectRepository $repository
    ) {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(3);
        $stockable->setOnHand(0)->shouldBeCalled();

        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $inventoryUnit3->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();

        $repository
            ->findBy(
                [
                    'stockable' => $stockable,
                    'inventoryState' => InventoryUnitInterface::STATE_BACKORDERED,
                ],
                [
                    'createdAt' => 'ASC',
                ]
            )
            ->willReturn([$inventoryUnit1, $inventoryUnit2, $inventoryUnit3])
        ;

        $this->fillBackorders($stockable);
    }

    function it_partially_fills_backordered_units_and_updates_stock_accordingly(
        StockableInterface $stockable,
        InventoryUnitInterface $inventoryUnit1,
        InventoryUnitInterface $inventoryUnit2,
        ObjectRepository $repository
    ) {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(5);
        $stockable->setOnHand(3)->shouldBeCalled();

        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();

        $repository
            ->findBy(
                [
                    'stockable' => $stockable,
                    'inventoryState' => InventoryUnitInterface::STATE_BACKORDERED,
                ],
                [
                    'createdAt' => 'ASC',
                ]
            )
            ->willReturn([$inventoryUnit1, $inventoryUnit2])
        ;

        $this->fillBackorders($stockable);
    }
}
