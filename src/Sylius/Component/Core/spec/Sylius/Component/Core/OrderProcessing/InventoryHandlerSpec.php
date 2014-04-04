<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Core\OrderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Core\Model\InventoryUnitInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Inventory\Factory\InventoryUnitFactory;
use Sylius\Component\Inventory\Operator\InventoryOperatorInterface;

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
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\InventoryHandler');
    }

    function it_implements_Sylius_inventory_handler_interface()
    {
        $this->shouldImplement('Sylius\Component\Core\OrderProcessing\InventoryHandlerInterface');
    }

    function it_creates_inventory_units_via_the_factory(
        $inventoryUnitFactory,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2
    )
    {
        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);

        $item->getInventoryUnits()->shouldBeCalled()->willReturn(new ArrayCollection());

        $inventoryUnitFactory->create($variant, 2, InventoryUnitInterface::STATE_CHECKOUT)->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $item->addInventoryUnit($unit1)->shouldBeCalled();
        $item->addInventoryUnit($unit2)->shouldBeCalled();

        $this->processInventoryUnits($item);
    }

    function it_creates_only_missing_inventory_units_via_the_factory(
        $inventoryUnitFactory,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2,
        ArrayCollection $units
    )
    {
        $item->getInventoryUnits()->shouldBeCalled()->willReturn(new ArrayCollection(array($unit1)));
        $unit1->getStockable()->willReturn($variant);
        $unit2->getStockable()->willReturn($variant);

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);

        $inventoryUnitFactory->create($variant, 1, InventoryUnitInterface::STATE_CHECKOUT)->shouldBeCalled()->willReturn(array($unit2));

        $item->addInventoryUnit($unit1)->shouldNotBeCalled();
        $item->addInventoryUnit($unit2)->shouldBeCalled();

        $this->processInventoryUnits($item);
    }

    function it_holds_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);
        $item->getInventoryUnits()->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $unit1->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_CHECKOUT);
        $unit1->setInventoryState(InventoryUnitInterface::STATE_ONHOLD)->shouldBeCalled();

        $unit2->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_ONHOLD);
        $unit2->setInventoryState(InventoryUnitInterface::STATE_ONHOLD)->shouldNotBeCalled();

        $inventoryOperator->hold($variant, 1)->shouldBeCalled();

        $this->holdInventory($order);
    }

    function it_releases_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);
        $item->getInventoryUnits()->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $unit1->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_ONHOLD);
        $unit1->setInventoryState(InventoryUnitInterface::STATE_CHECKOUT)->shouldBeCalled();

        $unit2->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_CHECKOUT);
        $unit2->setInventoryState(InventoryUnitInterface::STATE_CHECKOUT)->shouldNotBeCalled();

        $inventoryOperator->release($variant, 1)->shouldBeCalled();

        $this->releaseInventory($order);
    }

    function it_decreases_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2
    )
    {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);
        $item->getInventoryUnits()->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $unit1->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_ONHOLD);
        $unit1->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();

        $unit2->getInventoryState()->shouldBeCalled()->willReturn(InventoryUnitInterface::STATE_CHECKOUT);
        $unit2->setInventoryState(InventoryUnitInterface::STATE_SOLD)->shouldBeCalled();

        $inventoryOperator->decrease(array($unit1, $unit2))->shouldBeCalled();
        $inventoryOperator->release($variant, 1)->shouldBeCalled();

        $this->updateInventory($order);
    }
}
