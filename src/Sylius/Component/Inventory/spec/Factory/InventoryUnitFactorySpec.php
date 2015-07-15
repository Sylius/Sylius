<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Inventory\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Model\StockableInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryUnitFactorySpec extends ObjectBehavior
{
    public function let(RepositoryInterface $repository)
    {
        $this->beConstructedWith($repository);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Factory\InventoryUnitFactory');
    }

    public function it_implements_Sylius_inventory_unit_factory_interface()
    {
        $this->shouldImplement('Sylius\Component\Inventory\Factory\InventoryUnitFactoryInterface');
    }

    public function it_throws_exception_if_given_quantity_is_less_than_1(StockableInterface $stockable)
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringCreate($stockable, -2)
        ;
    }

    public function it_creates_inventory_units(
        StockableInterface $stockable,
        InventoryUnitInterface $inventoryUnit1,
        InventoryUnitInterface $inventoryUnit2,
        InventoryUnitInterface $inventoryUnit3,
        $repository
    ) {
        $repository->createNew()->shouldBeCalled()->willReturn($inventoryUnit1, $inventoryUnit2, $inventoryUnit3);

        $inventoryUnit1->setStockable($stockable)->shouldBeCalled();
        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED)->shouldBeCalled();

        $inventoryUnit2->setStockable($stockable)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED)->shouldBeCalled();

        $inventoryUnit3->setStockable($stockable)->shouldBeCalled();
        $inventoryUnit3->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED)->shouldBeCalled();

        $this->create($stockable, 3, InventoryUnitInterface::STATE_BACKORDERED);
    }
}
