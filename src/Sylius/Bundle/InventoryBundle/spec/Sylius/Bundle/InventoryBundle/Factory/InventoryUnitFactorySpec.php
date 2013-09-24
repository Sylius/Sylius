<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InventoryBundle\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryUnitFactorySpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface $repository
     */
    function let($repository)
    {
        $this->beConstructedWith($repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Factory\InventoryUnitFactory');
    }

    function it_implements_Sylius_inventory_unit_factory_interface()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Factory\InventoryUnitFactoryInterface');
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_throws_exception_if_given_quantity_is_less_than_1($stockable)
    {
        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringCreate($stockable, -2)
        ;
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit3
     */
    function it_creates_inventory_units($stockable, $inventoryUnit1, $inventoryUnit2, $inventoryUnit3, $repository)
    {
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
