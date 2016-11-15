<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Modifier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Component\Order\Factory\OrderItemUnitFactoryInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifier;
use Sylius\Component\Order\Modifier\OrderItemQuantityModifierInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderItemQuantityModifierSpec extends ObjectBehavior
{
    function let(OrderItemUnitFactoryInterface $orderItemUnitFactory)
    {
        $this->beConstructedWith($orderItemUnitFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderItemQuantityModifier::class);
    }

    function it_implements_an_order_item_quantity_modifier_interface()
    {
        $this->shouldImplement(OrderItemQuantityModifierInterface::class);
    }

    function it_adds_proper_number_of_order_item_units_to_an_order_item(
        OrderItemUnitFactoryInterface $orderItemUnitFactory,
        OrderItemInterface $orderItem
    ) {
        $orderItem->getQuantity()->willReturn(1);

        $orderItemUnitFactory->createForItem($orderItem)->shouldBeCalledTimes(2);

        $this->modify($orderItem, 3);
    }

    function it_removes_units_if_target_quantity_is_lower_than_current(
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2,
        OrderItemUnitInterface $unit3,
        OrderItemUnitInterface $unit4
    ) {
        $orderItem->getQuantity()->willReturn(4);
        $orderItem->getUnits()->willReturn([$unit1, $unit2, $unit3, $unit4]);
        $orderItem->removeUnit($unit1)->shouldBeCalled();

        $this->modify($orderItem, 3);
    }

    function it_does_nothing_if_target_quantity_is_equal_to_current(
        OrderItemUnitFactoryInterface $orderItemUnitFactory,
        OrderItemInterface $orderItem
    ) {
        $orderItem->getQuantity()->willReturn(3);

        $orderItemUnitFactory->createForItem(Argument::any())->shouldNotBeCalled();
        $orderItem->addUnit(Argument::any())->shouldNotBeCalled();
        $orderItem->removeUnit(Argument::any())->shouldNotBeCalled();

        $this->modify($orderItem, 3);
    }

    function it_does_nothing_if_target_quantity_is_0(
        OrderItemUnitFactoryInterface $orderItemUnitFactory,
        OrderItemInterface $orderItem
    ) {
        $orderItem->getQuantity()->willReturn(3);

        $orderItemUnitFactory->createForItem(Argument::any())->shouldNotBeCalled();
        $orderItem->addUnit(Argument::any())->shouldNotBeCalled();
        $orderItem->removeUnit(Argument::any())->shouldNotBeCalled();

        $this->modify($orderItem, 0);
    }

    function it_does_nothing_if_target_quantity_is_below_0(
        OrderItemUnitFactoryInterface $orderItemUnitFactory,
        OrderItemInterface $orderItem
    ) {
        $orderItem->getQuantity()->willReturn(3);

        $orderItemUnitFactory->createForItem(Argument::any())->shouldNotBeCalled();
        $orderItem->addUnit(Argument::any())->shouldNotBeCalled();
        $orderItem->removeUnit(Argument::any())->shouldNotBeCalled();

        $this->modify($orderItem, -10);
    }
}
