<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\CoreBundle\Modifier;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\CoreBundle\Modifier\OrderItemQuantityModifierInterface;
use Sylius\Component\Core\Model\OrderItemInterface;
use Sylius\Component\Core\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemQuantityModifierSpec extends ObjectBehavior
{
    function let(FactoryInterface $orderItemUnitFactory)
    {
        $this->beConstructedWith($orderItemUnitFactory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\CoreBundle\Modifier\OrderItemQuantityModifier');
    }

    function it_implements_order_item_quantity_modifier_interface()
    {
        $this->shouldImplement(OrderItemQuantityModifierInterface::class);
    }

    function it_adds_proper_number_of_order_item_units_to_order_item(
        $orderItemUnitFactory,
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $unit1,
        OrderItemUnitInterface $unit2
    ) {
        $orderItem->getQuantity()->willReturn(1);

        $orderItemUnitFactory->createNew()->willReturn($unit1, $unit2);

        $orderItem->addUnit($unit1)->shouldBeCalled();
        $orderItem->addUnit($unit2)->shouldBeCalled();

        $this->modify($orderItem, 3);
    }

    function it_removes_units_if_target_quantity_is_greater_than_current($orderItemUnitFactory, OrderItemInterface $orderItem)
    {
        $orderItem->getQuantity()->willReturn(5);

        $orderItemUnitFactory->createNew()->shouldNotBeCalled();

        $orderItem->removeUnitByIndex(4)->shouldBeCalled();
        $orderItem->removeUnitByIndex(3)->shouldBeCalled();

        $this->modify($orderItem, 3);
    }

    function it_does_nothing_if_target_quantity_is_equal_to_current($orderItemUnitFactory, OrderItemInterface $orderItem)
    {
        $orderItem->getQuantity()->willReturn(3);

        $orderItemUnitFactory->createNew()->shouldNotBeCalled();
        $orderItem->addUnit(Argument::any())->shouldNotBeCalled();
        $orderItem->removeUnitByIndex(Argument::any())->shouldNotBeCalled();
    }
}
