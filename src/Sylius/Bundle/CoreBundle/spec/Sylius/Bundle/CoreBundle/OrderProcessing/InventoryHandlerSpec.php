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

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Model\OrderItemInterface;
use Sylius\Bundle\CoreBundle\Model\VariantInterface;
use Sylius\Component\Inventory\Factory\InventoryUnitFactory;
use Sylius\Component\Inventory\Operator\InventoryOperatorInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryHandlerSpec extends ObjectBehavior
{
    function let(InventoryOperatorInterface $inventoryOperator, InventoryUnitFactory $inventoryUnitFactory)
    {
        $this->beConstructedWith($inventoryOperator, $inventoryUnitFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\OrderProcessing\InventoryHandler');
    }

    function it_implements_Sylius_inventory_handler_interface()
    {
        $this->shouldImplement('Sylius\Bundle\CoreBundle\OrderProcessing\InventoryHandlerInterface');
    }

    function it_does_not_create_any_inventory_units_if_order_has_no_items(OrderInterface $order)
    {
        $order->getItems()->willReturn(array());
        $order->addInventoryUnit(Argument::any())->shouldNotBeCalled();

        $this->processInventoryUnits($order);
    }

    function it_creates_inventory_units_via_the_factory(
        $inventoryUnitFactory, OrderInterface $order, OrderItemInterface $item, VariantInterface $variant, InventoryUnitInterface $unit1, InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);

        $order->getInventoryUnitsByVariant($variant)->shouldBeCalled()->willReturn(array());

        $inventoryUnitFactory->create($variant, 2, InventoryUnitInterface::STATE_CHECKOUT)->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $order->addInventoryUnit($unit1)->shouldBeCalled();
        $order->addInventoryUnit($unit2)->shouldBeCalled();

        $this->processInventoryUnits($order);
    }

    function it_creates_only_missing_inventory_units_via_the_factory(
        $inventoryUnitFactory, OrderInterface $order, OrderItemInterface $item, VariantInterface $variant, InventoryUnitInterface $unit1, InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);

        $order->getInventoryUnitsByVariant($variant)->shouldBeCalled()->willReturn(array($unit1));

        $inventoryUnitFactory->create($variant, 1, InventoryUnitInterface::STATE_CHECKOUT)->shouldBeCalled()->willReturn(array($unit2));

        $order->addInventoryUnit($unit1)->shouldNotBeCalled();
        $order->addInventoryUnit($unit2)->shouldBeCalled();

        $this->processInventoryUnits($order);
    }

    function it_removes_extra_inventory_units(
        $inventoryUnitFactory, OrderInterface $order, OrderItemInterface $item, VariantInterface $variant, InventoryUnitInterface $unit1, InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(1);

        $order->getInventoryUnitsByVariant($variant)->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $inventoryUnitFactory->create(Argument::any())->shouldNotBeCalled();

        $order->removeInventoryUnit($unit1)->shouldBeCalled();

        $this->processInventoryUnits($order);
    }

    function it_decreases_the_variant_stock_via_inventory_operator(
        $inventoryOperator, OrderInterface $order, OrderItemInterface $item, VariantInterface $variant, InventoryUnitInterface $unit1, InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(1);

        $order->getInventoryUnitsByVariant($variant)->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $inventoryOperator->decrease(array($unit1, $unit2))->shouldBeCalled();

        $this->updateInventory($order);
    }
}
