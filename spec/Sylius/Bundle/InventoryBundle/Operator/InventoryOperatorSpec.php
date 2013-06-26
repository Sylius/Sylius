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
 * @author Саша Стаменковић <umpirsky@gmail.com>
 */
class InventoryOperatorSpec extends ObjectBehavior
{
    /**
     * @param Doctrine\Common\Persistence\ObjectManager                          $manager
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface             $repository
     * @param Sylius\Bundle\InventoryBundle\Checker\AvailabilityCheckerInterface $availabilityChecker
     */
    function let($manager, $repository, $availabilityChecker)
    {
        $this->beConstructedWith($manager, $repository, $availabilityChecker);
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
    function it_decreases_stockable_on_hand($stockable, $inventoryUnit1, $inventoryUnit2, $repository, $availabilityChecker)
    {
        $availabilityChecker->isStockSufficient($stockable, 2)->willReturn(true);

        $stockable->getOnHand()->shouldBeCalled()->willReturn(7);
        $stockable->setOnHand(5)->shouldBeCalled();

        $inventoryUnit1->setStockable($stockable)->shouldBeCalled();
        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $repository->createNew()->willReturn($inventoryUnit1);

        $inventoryUnit2->setStockable($stockable)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $repository->createNew()->willReturn($inventoryUnit2);

        $this->decrease($stockable, 2);
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     */
    function it_backorders_units_if_quantity_is_greater_then_on_hand($stockable, $inventoryUnit1, $inventoryUnit2, $repository, $availabilityChecker)
    {
        $availabilityChecker->isStockSufficient($stockable, 2)->willReturn(true);

        $stockable->getOnHand()->shouldBeCalled()->willReturn(1);
        $stockable->setOnHand(0)->shouldBeCalled();

        $repository->createNew()->willReturn($inventoryUnit1);
        $inventoryUnit1->setStockable($stockable)->shouldBeCalled();
        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();

        $repository->createNew()->willReturn($inventoryUnit2);
        $inventoryUnit2->setStockable($stockable)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_BACKORDERED)->shouldBeCalled();

        $this->decrease($stockable, 2)->shouldHaveType('Doctrine\Common\Collections\ArrayCollection');
    }

    /**
     * @param Sylius\Bundle\InventoryBundle\Model\StockableInterface     $stockable
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit1
     * @param Sylius\Bundle\InventoryBundle\Model\InventoryUnitInterface $inventoryUnit2
     * @param Doctrine\Common\Persistence\ObjectRepository               $repository
     */
    function it_fills_backorder_units($stockable, $inventoryUnit1, $inventoryUnit2, $repository)
    {
        $stockable->getOnHand()->shouldBeCalled()->willReturn(1);
        $stockable->setOnHand(0)->shouldBeCalled();

        $inventoryUnit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $inventoryUnit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldNotBeCalled();

        $repository->findBy(Argument::any(), Argument::any())->willReturn(array($inventoryUnit1, $inventoryUnit2));

        $this->fillBackorders($stockable);
    }
}
