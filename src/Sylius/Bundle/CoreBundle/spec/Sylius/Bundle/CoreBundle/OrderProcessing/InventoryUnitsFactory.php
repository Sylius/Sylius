<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\OrderProcessing;

use PHPSpec2\ObjectBehavior;

/**
 * Inventory units factory spec.
 *
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryUnitsFactory extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\InventoryBundle\Operator\InventoryOperatorInterface $inventoryOperator
     */
    function let($inventoryOperator)
    {
        $this->beConstructedWith($inventoryOperator);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\OrderProcessing\InventoryUnitsFactory');
    }

    function it_implements_Sylius_inventory_units_factory()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\OrderProcessing\InventoryUnitsFactoryInterface');
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface $order
     */
    function it_does_not_create_any_inventory_units_if_order_has_no_items($order)
    {
        $order->getItems()->willReturn(array());
        $order->addInventoryUnit(ANY_ARGUMENT)->shouldNotBeCalled();

        $this->createInventoryUnits($order);
    }

    /**
     * @param Sylius\Bundle\CoreBundle\Model\OrderInterface         $order
     * @param Sylius\Bundle\CoreBundle\Entity\OrderItem             $item
     * @param Sylius\Bundle\CoreBundle\Model\VariantInterface       $variant
     * @param Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface $unitA
     * @param Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface $unitB
     */
    function it_creates_inventory_units_by_inventory_operator($inventoryOperator, $order, $item, $variant, $unitA, $unitB)
    {
        $order->getItems()->willReturn(array($item));
        $item->getSellable()->willReturn($variant);
        $item->getQuantity()->willReturn(2);

        $inventoryOperator->decrease($variant, 2)->shouldBeCalled()->willReturn(array($unitA, $unitB));

        $order->addInventoryUnit($unitA)->shouldBeCalled();
        $order->addInventoryUnit($unitB)->shouldBeCalled();

        $this->createInventoryUnits($order);
    }
}
