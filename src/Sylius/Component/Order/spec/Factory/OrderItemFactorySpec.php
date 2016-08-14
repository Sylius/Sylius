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
use Sylius\Component\Resource\Repository\RepositoryInterface;

/**
 * @author Mateusz Zalewski <mateusz.zalewski@lakion.com>
 */
class OrderItemFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedFactory, RepositoryInterface $orderRepository)
    {
        $this->beConstructedWith($decoratedFactory, $orderRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Component\Order\Factory\OrderItemFactory');
    }

    function it_implements_order_item_factory_interface()
    {
        $this->shouldImplement(OrderItemFactoryInterface::class);
    }

    function it_uses_decorated_factory_to_create_new_order_item(
        FactoryInterface $decoratedFactory,
        OrderItemInterface $item
    ) {
        $decoratedFactory->createNew()->willReturn($item);

        $this->createNew()->shouldReturn($item);
    }

    function it_creates_order_item_for_order_with_id(
        FactoryInterface $decoratedFactory,
        OrderInterface $order,
        OrderItemInterface $item,
        RepositoryInterface $orderRepository
    ) {
        $decoratedFactory->createNew()->willReturn($item);
        $orderRepository->find(1)->willReturn($order);

        $item->setOrder($order)->shouldBeCalled();

        $this->createForOrderWithId(1)->shouldReturn($item);
    }

    function it_throws_exception_of_order_with_given_id_does_not_exist(RepositoryInterface $orderRepository)
    {
        $orderRepository->find(10)->willReturn(null);

        $this->shouldThrow(\InvalidArgumentException::class)->during('createForOrderWithId', [10]);
    }
}
