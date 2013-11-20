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
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class InventoryOperatorSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\InventoryBundle\Operator\BackordersHandlerInterface  $backordersHandler
     * @param Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface $availabilityChecker
     */
    function let($backordersHandler, $availabilityChecker)
    {
        $this->beConstructedWith($backordersHandler, $availabilityChecker);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Operator\InventoryOperator');
   }

    function it_implements_Sylius_inventory_operator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Operator\InventoryOperatorInterface');
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_increases_stockable_on_hand($stockable)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(2);
        $stockable->setOnHand(7)->shouldBeCalled();

        $this->increase($stockable, 5);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     */
    function it_decreases_stockable_on_hand_by_count_of_sold_units($availabilityChecker, $backordersHandler, $stockable, $inventoryUnit1, $inventoryUnit2)
    {
        $inventoryUnit1->getStockable()->willReturn($stockable);
        $inventoryUnit2->getStockable()->willReturn($stockable);

        $availabilityChecker->isStockSufficient($stockable, 2)->shouldBeCalled()->willReturn(true);
        $backordersHandler->processBackorders(array($inventoryUnit1, $inventoryUnit2))->shouldBeCalled();

        $inventoryUnit1->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_SOLD);
        $inventoryUnit2->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_SOLD);

        $stockable->getOnHand()->shouldBeCalled()->willReturn(7);
        $stockable->setOnHand(5)->shouldBeCalled();

        $this->decrease(array($inventoryUnit1, $inventoryUnit2));
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit3
     */
    function it_decreases_stockable_on_hand_and_ignores_backordered_units($availabilityChecker, $backordersHandler, $stockable, $inventoryUnit1, $inventoryUnit2, $inventoryUnit3)
    {
        $inventoryUnit1->getStockable()->willReturn($stockable);
        $inventoryUnit2->getStockable()->willReturn($stockable);
        $inventoryUnit3->getStockable()->willReturn($stockable);

        $availabilityChecker->isStockSufficient($stockable, 3)->shouldBeCalled()->willReturn(true);
        $backordersHandler->processBackorders(array($inventoryUnit1, $inventoryUnit2, $inventoryUnit3))->shouldBeCalled();

        $inventoryUnit1->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_SOLD);
        $inventoryUnit2->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_BACKORDERED);
        $inventoryUnit3->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_BACKORDERED);

        $stockable->getOnHand()->shouldBeCalled()->willReturn(1);
        $stockable->setOnHand(0)->shouldBeCalled();

        $this->decrease(array($inventoryUnit1, $inventoryUnit2, $inventoryUnit3));
    }
}
