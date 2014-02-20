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

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Model\OrderInterface;
use Sylius\Bundle\CoreBundle\Model\OrderItem;
use Sylius\Bundle\CoreBundle\Model\VariantInterface;
use Sylius\Bundle\CoreBundle\Model\InventoryUnitInterface;
use Sylius\Bundle\InventoryBundle\Factory\InventoryUnitFactory;
use Sylius\Bundle\InventoryBundle\Operator\InventoryOperatorInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class InventoryHandlerSpec extends ObjectBehavior
{
    function let(
        InventoryOperatorInterface $inventoryOperator,
        InventoryUnitFactory $inventoryUnitFactory
    )
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
        $order->getInventoryUnits()->willReturn(array());
        $order->addInventoryUnit(Argument::any())->shouldNotBeCalled();

        $this->processInventoryUnits($order);
    }

    function it_creates_inventory_units_via_the_factory(
        $inventoryUnitFactory,
        OrderInterface $order,
        OrderItem $item,
        VariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));
        $order->getInventoryUnits()->shouldBeCalled()->willReturn(array());

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);

        $order->getInventoryUnitsByVariant($variant)->shouldBeCalled()->willReturn(array());

        $inventoryUnitFactory->create($variant, 2, InventoryUnitInterface::STATE_CHECKOUT)->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $order->addInventoryUnit($unit1)->shouldBeCalled();
        $order->addInventoryUnit($unit2)->shouldBeCalled();

        $this->processInventoryUnits($order);
    }

    function it_creates_only_missing_inventory_units_via_the_factory(
        $inventoryUnitFactory,
        OrderInterface $order,
        OrderItem $item,
        VariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));
        $order->getInventoryUnits()->shouldBeCalled()->willReturn(array($unit1, $unit2));
        $unit1->getStockable()->shouldBeCalled()->willReturn($variant);
        $unit2->getStockable()->shouldBeCalled()->willReturn($variant);

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);

        $order->getInventoryUnitsByVariant($variant)->shouldBeCalled()->willReturn(array($unit1));

        $inventoryUnitFactory->create($variant, 1, InventoryUnitInterface::STATE_CHECKOUT)->shouldBeCalled()->willReturn(array($unit2));

        $order->addInventoryUnit($unit1)->shouldNotBeCalled();
        $order->addInventoryUnit($unit2)->shouldBeCalled();

        $this->processInventoryUnits($order);
    }

    function it_removes_extra_inventory_units(
        $inventoryUnitFactory,
        OrderInterface $order,
        OrderItem $item,
        VariantInterface $variant,
        ArrayCollection $units,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2,
        InventoryUnitInterface $unit3
    )
    {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(1);
        $units->count()->willReturn(2);

        $order->getInventoryUnitsByVariant($variant)->shouldBeCalled()->willReturn($units);
        $order->getInventoryUnits()->shouldBeCalled()->willReturn(array($unit3));
        $units->slice(0, 1)->shouldBeCalled()->willReturn(array($unit2));

        $inventoryUnitFactory->create(Argument::any())->shouldNotBeCalled();

        $order->removeInventoryUnit($unit2)->shouldBeCalled();
        $order->removeInventoryUnit($unit3)->shouldBeCalled();

        $this->processInventoryUnits($order);
    }

    function it_holds_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        OrderInterface $order,
        OrderItem $item,
        VariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(1);

        $order->getInventoryUnitsByVariant($variant)->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $unit1->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_CHECKOUT);
        $unit2->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_CHECKOUT);
        $unit1->setInventoryState(InventoryUnitInterface::STATE_ONHOLD)->shouldBeCalled();
        $unit2->setInventoryState(InventoryUnitInterface::STATE_ONHOLD)->shouldBeCalled();

        $inventoryOperator->hold($variant, 1)->shouldBeCalled();

        $this->holdInventory($order);
    }

    function it_releases_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        OrderInterface $order,
        OrderItem $item,
        VariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(1);

        $order->getInventoryUnitsByVariant($variant)->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $unit1->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_ONHOLD);
        $unit2->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_ONHOLD);
        $unit1->setInventoryState(InventoryUnitInterface::STATE_CHECKOUT)->shouldBeCalled();
        $unit2->setInventoryState(InventoryUnitInterface::STATE_CHECKOUT)->shouldBeCalled();

        $inventoryOperator->release($variant, 1)->shouldBeCalled();

        $this->releaseInventory($order);
    }

    function it_decreases_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        OrderInterface $order,
        OrderItem $item,
        VariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(1);

        $order->getInventoryUnitsByVariant($variant)->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $unit1->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_ONHOLD);
        $unit2->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_ONHOLD);
        $unit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();
        $unit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();

        $inventoryOperator->decrease(array($unit1, $unit2))->shouldBeCalled();
        $inventoryOperator->release($variant, 1)->shouldBeCalled();

        $this->updateInventory($order);
    }
}
