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
use Sylius\Component\Resource\Factory\ResourceFactoryInterface;
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @mixin \Sylius\Component\Inventory\Factory\InventoryUnitFactory
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryUnitFactorySpec extends ObjectBehavior
{
    function let(ResourceFactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Inventory\Factory\InventoryUnitFactory');
    }

    function it_implements_inventory_unit_factory_interface()
    {
        $this->shouldImplement('Sylius\Component\Inventory\Factory\InventoryUnitFactoryInterface');
    }

    function it_throws_exception_if_given_quantity_is_less_than_1(StockableInterface $stockable)
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringCreateForStockable($stockable, -2)
        ;
    }

    function it_creates_units_for_given_stockable(ResourceFactoryInterface $factory, StockableInterface $stockable, InventoryUnitInterface $unit1, InventoryUnitInterface $unit2)
    {
        $factory->createNew()->shouldBeCalled()->willReturn($unit1, $unit2);

        $unit1->setStockable($stockable)->shouldBeCalled();
        $unit1->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED)->shouldBeCalled();
        $unit2->setStockable($stockable)->shouldBeCalled();
        $unit2->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED)->shouldBeCalled();

        $this->createForStockable($stockable, 2, InventoryUnitInterface::STATE_BACKORDERED)->shouldHaveType('Doctrine\Common\Collections\ArrayCollection');
    }
}
