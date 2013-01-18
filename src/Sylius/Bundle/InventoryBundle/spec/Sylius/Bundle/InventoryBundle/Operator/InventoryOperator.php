<?php

namespace spec\Sylius\Bundle\InventoryBundle\Operator;

use PHPSpec2\ObjectBehavior;
use Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface;

/**
 * Inventory operator spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class InventoryOperator extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectManager                          $manager
     * @param Doctrine\Common\Persistence\ObjectRepository                       $repository
     * @param Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface $availabilityChecker
     */
    function let($manager, $repository, $availabilityChecker)
    {
        $this->beConstructedWith($manager, $repository, $availabilityChecker);
    }

    function it_should_be_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\InventoryBundle\Operator\InventoryOperator');
    }

    function it_should_be_Sylius_inventory_operator()
    {
        $this->shouldImplement('Sylius\Bundle\InventoryBundle\Operator\InventoryOperatorInterface');
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_increase_stockable_on_hand($stockable)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(2);
        $stockable->setOnHand(7)->shouldBeCalled();

        $this->increase($stockable, 5);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface $stockable
     */
    function it_should_decrease_stockable_on_hand($stockable)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(7);
        $stockable->setOnHand(5)->shouldBeCalled();

        $this->decrease($stockable, 2);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     * @param Doctrine\Common\Persistence\ObjectRepository               $repository
     */
    function it_should_backorder_units_if_quantity_is_greater_then_on_hand($stockable, $inventoryUnit1, $inventoryUnit2, $repository)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(1);
        $stockable->setOnHand(0)->shouldBeCalled();
        $inventoryUnit1->setStockable($stockable)->shouldBeCalled();
        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $repository->createNew()->willReturn($inventoryUnit1);
        $inventoryUnit2->setStockable($stockable)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED)->shouldBeCalled();
        $repository->createNew()->willReturn($inventoryUnit2);

        $this->decrease($stockable, 2)->shouldHaveType('Doctrine\Common\Collections\ArrayCollection');
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     * @param Doctrine\Common\Persistence\ObjectRepository               $repository
     */
    function it_should_fill_backorder_units($stockable, $inventoryUnit1, $inventoryUnit2, $repository)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(1);
        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldNotBeCalled();
        $repository->findBy(ANY_ARGUMENTS)->willReturn(array($inventoryUnit1, $inventoryUnit2));

        $this->fillBackorders($stockable);
    }
}
