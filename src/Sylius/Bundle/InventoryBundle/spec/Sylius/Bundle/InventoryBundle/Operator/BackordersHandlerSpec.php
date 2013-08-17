<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\InventoryBundle\Operator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class BackordersHandlerSpec extends ObjectBehavior
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
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Operator\BackordersHandler');
   }

    function it_implements_Sylius_inventory_backorders_handler_interface()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Operator\BackordersHandlerInterface');
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit3
     */
    function it_backorders_units_if_quantity_is_greater_than_on_hand($stockable, $inventoryUnit1, $inventoryUnit2, $inventoryUnit3)
    {
        $inventoryUnit1->getStockable()->shouldBeCalled()->willReturn($stockable);
        $inventoryUnit2->getStockable()->shouldBeCalled()->willReturn($stockable);
        $inventoryUnit3->getStockable()->shouldBeCalled()->willReturn($stockable);

        $stockable->getOnHand()->willReturn(2);

        $inventoryUnit1->setInventoryState(Argument::any())->shouldNotBeCalled();
        $inventoryUnit2->setInventoryState(Argument::any())->shouldNotBeCalled();
        $inventoryUnit3->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED)->shouldBeCalled();

        $this->processBackorders(array($inventoryUnit1, $inventoryUnit2, $inventoryUnit3));
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable1
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable2
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     */
    function it_complains_if_inventory_units_contain_different_stockables($stockable1, $stockable2, $inventoryUnit1, $inventoryUnit2)
    {
        $inventoryUnit1->getStockable()->shouldBeCalled()->willReturn($stockable1);
        $inventoryUnit2->getStockable()->shouldBeCalled()->willReturn($stockable2);

        $stockable1->getOnHand()->shouldBeCalled()->willReturn(50);

        $this
            ->shouldThrow('InvalidArgumentException')
            ->duringProcessBackorders(array($inventoryUnit1, $inventoryUnit2))
        ;
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     * @param Doctrine\Common\Persistence\ObjectRepository               $repository
     */
    function it_partially_fills_backordered_units_if_not_enough_in_stock($stockable, $inventoryUnit1, $inventoryUnit2, $repository)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(1);
        $stockable->setOnHand(0)->shouldBeCalled();

        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldNotBeCalled();

        $repository
            ->findBy(
                array(
                    'stockable'      => $stockable,
                    'inventoryState' => InventoryUnitInterface::STATE_BACKORDERED
                ),
                array(
                    'createdAt' => 'ASC'
                )
            )
            ->willReturn(array($inventoryUnit1, $inventoryUnit2))
        ;

        $this->fillBackorders($stockable);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit3
     * @param Doctrine\Common\Persistence\ObjectRepository               $repository
     */
    function it_fills_all_backordered_units_if_enough_in_stock($stockable, $inventoryUnit1, $inventoryUnit2, $inventoryUnit3, $repository)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(3);
        $stockable->setOnHand(0)->shouldBeCalled();

        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $inventoryUnit3->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();

        $repository
            ->findBy(
                array(
                    'stockable'      => $stockable,
                    'inventoryState' => InventoryUnitInterface::STATE_BACKORDERED
                ),
                array(
                    'createdAt' => 'ASC'
                )
            )
            ->willReturn(array($inventoryUnit1, $inventoryUnit2, $inventoryUnit3))
        ;

        $this->fillBackorders($stockable);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     * @param Doctrine\Common\Persistence\ObjectRepository               $repository
     */
    function it_partially_fills_backordered_units_and_updates_stock_accordingly($stockable, $inventoryUnit1, $inventoryUnit2, $repository)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(5);
        $stockable->setOnHand(3)->shouldBeCalled();

        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();

        $repository
            ->findBy(
                array(
                    'stockable'      => $stockable,
                    'inventoryState' => InventoryUnitInterface::STATE_BACKORDERED
                ),
                array(
                    'createdAt' => 'ASC'
                )
            )
            ->willReturn(array($inventoryUnit1, $inventoryUnit2))
        ;

        $this->fillBackorders($stockable);
    }
}
