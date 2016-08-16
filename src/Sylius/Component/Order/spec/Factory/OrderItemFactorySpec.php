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
use Sylius\Component\Order\Factory\OrderItemFactoryInterface;
use Sylius\Component\Order\Model\OrderInterface;
use Sylius\Component\Order\Model\OrderItemInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

/**
 * @author Daniel Gorgan <danut007ro@gmail.com>
 */
final class OrderItemFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $factory)
    {
        $this->beConstructedWith($factory);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Order\Factory\OrderItemFactory');
    }

    function it_implements_order_item_factory_interface()
    {
        $this->shouldImplement(OrderItemFactoryInterface::class);
    }

    function it_creates_new_order_item(FactoryInterface $factory, OrderItemInterface $orderItem)
    {
        $factory->createNew()->willReturn($orderItem);

        $this->createNew()->shouldReturn($orderItem);
    }

    function it_creates_new_order_item_for_given_order(FactoryInterface $factory, OrderInterface $order, OrderItemInterface $orderItem)
    {
        $factory->createNew()->willReturn($orderItem);
        $orderItem->setOrder($order)->shouldBeCalled();

        $this->createForOrder($order);
    }
}
