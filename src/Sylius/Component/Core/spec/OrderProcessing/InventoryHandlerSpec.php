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
use SM\Factory\FactoryInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\ProductVariantInterface;
use Sylius\Component\Core\OrderProcessing\InventoryHandlerInterface;
use Sylius\Component\Inventory\Factory\InventoryUnitFactoryInterface;
use Sylius\Component\Inventory\InventoryUnitTransitions;
use Sylius\Component\Inventory\Model\InventoryUnitInterface;
use Sylius\Component\Inventory\Operator\InventoryOperatorInterface;
use Sylius\Component\Resource\StateMachine\StateMachineInterface;

/**
 * @mixin \Sylius\Component\Core\OrderProcessing\InventoryHandler
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryHandlerSpec extends ObjectBehavior
{
    function let(
        InventoryOperatorInterface $inventoryOperator,
        InventoryUnitFactoryInterface $inventoryUnitFactory,
        FactoryInterface $factory
    ) {
        $this->beConstructedWith($inventoryOperator, $inventoryUnitFactory, $factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Core\OrderProcessing\InventoryHandler');
    }

    function it_implements_Sylius_inventory_handler_interface()
    {
        $this->shouldImplement(InventoryHandlerInterface::class);
    }

    function it_creates_inventory_units_via_the_factory(
        InventoryUnitFactoryInterface $inventoryUnitFactory,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2
    ) {
        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);

        $item->getInventoryUnits()->willReturn(new ArrayCollection());
        $inventoryUnitFactory->createForStockable($variant, 2, InventoryUnitInterface::STATE_CHECKOUT)->willReturn(array($unit1, $unit2));

        $item->addInventoryUnit($unit1)->shouldBeCalled();
        $item->addInventoryUnit($unit2)->shouldBeCalled();

        $this->processInventoryUnits($item);
    }

    function it_creates_only_missing_inventory_units_via_the_factory(
        InventoryUnitFactoryInterface $inventoryUnitFactory,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2
    ) {
        $item->getInventoryUnits()->shouldBeCalled()->willReturn(new ArrayCollection(array($unit1)));
        $unit1->getStockable()->willReturn($variant);
        $unit2->getStockable()->willReturn($variant);

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);

        $inventoryUnitFactory->createForStockable($variant, 1, InventoryUnitInterface::STATE_CHECKOUT)->willReturn(array($unit2));

        $item->addInventoryUnit($unit1)->shouldNotBeCalled();
        $item->addInventoryUnit($unit2)->shouldBeCalled();

        $this->processInventoryUnits($item);
    }

    function it_holds_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        $factory,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2,
        StateMachineInterface $sm1,
        StateMachineInterface $sm2
    ) {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);
        $item->getInventoryUnits()->willReturn(new ArrayCollection(array($unit1, $unit2)));

        $factory->get($unit1, InventoryUnitTransitions::GRAPH)->willReturn($sm1);
        $sm1->can(InventoryUnitTransitions::SYLIUS_HOLD)->willReturn(false);
        $sm1->apply(InventoryUnitTransitions::SYLIUS_HOLD)->shouldNotBeCalled();

        $factory->get($unit2, InventoryUnitTransitions::GRAPH)->willReturn($sm2);
        $sm1->can(InventoryUnitTransitions::SYLIUS_HOLD)->willReturn(true);
        $sm1->apply(InventoryUnitTransitions::SYLIUS_HOLD)->shouldBeCalled();

        $inventoryOperator->hold($variant, 1)->shouldBeCalled();

        $this->holdInventory($order);
    }

    function it_releases_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        $factory,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2,
        StateMachineInterface $sm1,
        StateMachineInterface $sm2
    ) {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);
        $item->getInventoryUnits()->willReturn(new ArrayCollection(array($unit1, $unit2)));

        $factory->get($unit1, InventoryUnitTransitions::GRAPH)->willReturn($sm1);
        $sm1->can(InventoryUnitTransitions::SYLIUS_RELEASE)->willReturn(false);
        $sm1->apply(InventoryUnitTransitions::SYLIUS_RELEASE)->shouldNotBeCalled();

        $factory->get($unit2, InventoryUnitTransitions::GRAPH)->willReturn($sm2);
        $sm1->can(InventoryUnitTransitions::SYLIUS_RELEASE)->willReturn(true);
        $sm1->apply(InventoryUnitTransitions::SYLIUS_RELEASE)->shouldBeCalled();

        $inventoryOperator->release($variant, 1)->shouldBeCalled();

        $this->releaseInventory($order);
    }

    function it_decreases_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        $factory,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        InventoryUnitInterface $unit1,
        InventoryUnitInterface $unit2,
        StateMachineInterface $sm1,
        StateMachineInterface $sm2
    ) {
        $order->getItems()->willReturn(array($item));

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);
        $item->getInventoryUnits()->shouldBeCalled()->willReturn(array($unit1, $unit2));

        $factory->get($unit1, InventoryUnitTransitions::GRAPH)->willReturn($sm1);
        $sm1->can(InventoryUnitTransitions::SYLIUS_SELL)->willReturn(true);
        $sm1->can(InventoryUnitTransitions::SYLIUS_RELEASE)->willReturn(true);
        $sm1->apply(InventoryUnitTransitions::SYLIUS_SELL)->shouldBeCalled();

        $factory->get($unit2, InventoryUnitTransitions::GRAPH)->willReturn($sm2);
        $sm2->can(InventoryUnitTransitions::SYLIUS_SELL)->willReturn(true);
        $sm2->can(InventoryUnitTransitions::SYLIUS_RELEASE)->willReturn(false);
        $sm2->apply(InventoryUnitTransitions::SYLIUS_SELL)->shouldBeCalled();

        $inventoryOperator->decrease(array($unit1, $unit2))->shouldBeCalled();
        $inventoryOperator->release($variant, 1)->shouldBeCalled();

        $this->updateInventory($order);
    }
}
