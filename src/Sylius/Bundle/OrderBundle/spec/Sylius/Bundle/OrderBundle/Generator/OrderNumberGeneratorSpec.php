<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\OrderBundle\Generator;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\OrderBundle\Model\OrderInterface;
use Sylius\Bundle\OrderBundle\Repository\OrderRepositoryInterface;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderNumberGeneratorSpec extends ObjectBehavior
{
    public function let(OrderRepositoryInterface $orderRepository)
    {
        $this->beConstructedWith($orderRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\OrderBundle\Generator\OrderNumberGenerator');
    }

    function it_implements_Sylius_order_number_generator_interface()
    {
        $this->shouldImplement('Sylius\Bundle\OrderBundle\Generator\OrderNumberGeneratorInterface');
    }

    function it_generates_000001_number_for_first_order($orderRepository, OrderInterface $order)
    {
        $order->getNumber()->willReturn(null);

        $orderRepository->findRecentOrders(1)->willReturn(array());
        $order->setNumber('000001')->shouldBeCalled();

        $this->generate($order);
    }

    function it_generates_a_correct_number_for_following_orders($orderRepository, OrderInterface $order, OrderInterface $lastOrder)
    {
        $order->getNumber()->willReturn(null);

        $orderRepository->findRecentOrders(1)->willReturn(array($lastOrder));
        $lastOrder->getNumber()->willReturn('000469');
        $order->setNumber('000470')->shouldBeCalled();

        $this->generate($order);
    }

    function it_starts_at_start_number_if_specified($orderRepository, OrderInterface $order)
    {
        $this->beConstructedWith($orderRepository, 9, 123);
        $order->getNumber()->willReturn(null);
        $order->setNumber('000000123')->shouldBeCalled();

        $this->generate($order);
    }

    function it_leaves_existing_numbers_alone(OrderInterface $order)
    {
        $order->getNumber()->willReturn('123');
        $order->setNumber(Argument::any())->shouldNotBeCalled();

        $this->generate($order);
    }
}
