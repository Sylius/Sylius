<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Component\Order\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Order\Factory\OrderItemUnitFactory;
use Sylius\Component\Order\Factory\OrderItemUnitFactoryInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Order\Model\OrderItemUnit;
use Sylius\Component\Order\Model\OrderItemUnitInterface;
use Sylius\Component\Resource\Exception\UnsupportedMethodException;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
final class OrderItemUnitFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(OrderItemUnit::class);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(OrderItemUnitFactory::class);
    }

    function it_implements_a_factory_interface()
    {
        $this->shouldImplement(OrderItemUnitFactoryInterface::class);
    }

    function it_throws_an_exception_while_trying_create_order_item_unit_without_order_item()
    {
        $this->shouldThrow(UnsupportedMethodException::class)->during('createNew');
    }

    function it_creates_a_new_order_item_unit_with_given_order_item(
        OrderItemInterface $orderItem,
        OrderItemUnitInterface $orderItemUnit
    ) {
        $orderItemUnit->getOrderItem()->willReturn($orderItem);

        $this->createForItem($orderItem)->shouldBeSameAs($orderItemUnit);
    }

    public function getMatchers()
    {
        return [
            'beSameAs' => function ($subject, $key) {
                if (!$subject instanceof OrderItemUnitInterface || !$key instanceof OrderItemUnitInterface) {
                    return false;
                }

                return $subject->getOrderItem() === $key->getOrderItem();
            },
        ];
    }
}
