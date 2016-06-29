<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Core\OrderProcessing;

use Doctrine\Common\Collections\ArrayCollection;
use PhpSpec\ObjectBehavior;
use SM\Factory\FactoryInterface as StateMachineFactoryInterface;
use Sylius\Order\Factory\OrderItemUnitFactoryInterface;
use Sylius\Core\Model\OrderInterface;
use Sylius\Core\Model\OrderItemInterface;
use Sylius\Core\Model\OrderItemUnitInterface;
use Sylius\Core\Model\ProductVariantInterface;
use Sylius\Core\OrderProcessing\InventoryHandlerInterface;
use Sylius\Inventory\InventoryUnitTransitions;
use Sylius\Inventory\Operator\InventoryOperatorInterface;
use Sylius\Resource\StateMachine\StateMachineInterface;

/**
 * @mixin \Sylius\Core\OrderProcessing\InventoryHandler
 *
 * @author Paweł Jędrzejewski <pawel@sylius.org>
 */
class InventoryHandlerSpec extends ObjectBehavior
{
    function let(
        InventoryOperatorInterface $inventoryOperator,
        OrderItemUnitFactoryInterface $orderItemUnitFactory,
        StateMachineFactoryInterface $stateMachineFactory
    ) {
        $this->beConstructedWith($inventoryOperator, $orderItemUnitFactory, $stateMachineFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Core\OrderProcessing\InventoryHandler');
    }

    function it_implements_Sylius_inventory_handler_interface()
    {
        $this->shouldImplement(InventoryHandlerInterface::class);
    }

    function it_holds_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        $stateMachineFactory,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        StateMachineInterface $sm1,
        StateMachineInterface $sm2
    ) {
        $order->getItems()->willReturn([$item]);

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);
        $item->getUnits()->willReturn(new ArrayCollection([$unit1, $unit2]));

        $stateMachineFactory->get($unit1, InventoryUnitTransitions::GRAPH)->willReturn($sm1);
        $sm1->can(InventoryUnitTransitions::SYLIUS_HOLD)->willReturn(false);
        $sm1->apply(InventoryUnitTransitions::SYLIUS_HOLD)->shouldNotBeCalled();

        $stateMachineFactory->get($unit2, InventoryUnitTransitions::GRAPH)->willReturn($sm2);
        $sm1->can(InventoryUnitTransitions::SYLIUS_HOLD)->willReturn(true);
        $sm1->apply(InventoryUnitTransitions::SYLIUS_HOLD)->shouldBeCalled();

        $inventoryOperator->hold($variant, 1)->shouldBeCalled();

        $this->holdInventory($order);
    }

    function it_releases_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        $stateMachineFactory,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        StateMachineInterface $sm1,
        StateMachineInterface $sm2
    ) {
        $order->getItems()->willReturn([$item]);

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);
        $item->getUnits()->willReturn(new ArrayCollection([$unit1, $unit2]));

        $stateMachineFactory->get($unit1, InventoryUnitTransitions::GRAPH)->willReturn($sm1);
        $sm1->can(InventoryUnitTransitions::SYLIUS_RELEASE)->willReturn(false);
        $sm1->apply(InventoryUnitTransitions::SYLIUS_RELEASE)->shouldNotBeCalled();

        $stateMachineFactory->get($unit2, InventoryUnitTransitions::GRAPH)->willReturn($sm2);
        $sm1->can(InventoryUnitTransitions::SYLIUS_RELEASE)->willReturn(true);
        $sm1->apply(InventoryUnitTransitions::SYLIUS_RELEASE)->shouldBeCalled();

        $inventoryOperator->release($variant, 1)->shouldBeCalled();

        $this->releaseInventory($order);
    }

    function it_decreases_the_variant_stock_via_inventory_operator(
        $inventoryOperator,
        $stateMachineFactory,
        OrderInterface $order,
        OrderItemInterface $item,
        ProductVariantInterface $variant,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        StateMachineInterface $sm1,
        StateMachineInterface $sm2
    ) {
        $order->getItems()->willReturn([$item]);

        $item->getVariant()->willReturn($variant);
        $item->getQuantity()->willReturn(2);
        $item->getUnits()->shouldBeCalled()->willReturn([$unit1, $unit2]);

        $stateMachineFactory->get($unit1, InventoryUnitTransitions::GRAPH)->willReturn($sm1);
        $sm1->can(InventoryUnitTransitions::SYLIUS_SELL)->willReturn(true);
        $sm1->can(InventoryUnitTransitions::SYLIUS_RELEASE)->willReturn(true);
        $sm1->apply(InventoryUnitTransitions::SYLIUS_SELL)->shouldBeCalled();

        $stateMachineFactory->get($unit2, InventoryUnitTransitions::GRAPH)->willReturn($sm2);
        $sm2->can(InventoryUnitTransitions::SYLIUS_SELL)->willReturn(true);
        $sm2->can(InventoryUnitTransitions::SYLIUS_RELEASE)->willReturn(false);
        $sm2->apply(InventoryUnitTransitions::SYLIUS_SELL)->shouldBeCalled();

        $inventoryOperator->decrease([$unit1, $unit2])->shouldBeCalled();
        $inventoryOperator->release($variant, 1)->shouldBeCalled();

        $this->updateInventory($order);
    }
}
