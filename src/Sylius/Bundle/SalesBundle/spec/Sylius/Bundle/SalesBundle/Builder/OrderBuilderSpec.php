<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace spec\Sylius\Bundle\SalesBundle\Builder;

use PhpSpec\ObjectBehavior;

/**
 * @author Paweł Jędrzejewski <pjedrzejewski@diweb.pl>
 */
class OrderBuilderSpec extends ObjectBehavior
{
    /**
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface $orderRepository
     * @param Sylius\Bundle\ResourceBundle\Model\RepositoryInterface $itemRepository
     */
    function let($orderRepository, $itemRepository)
    {
        $this->beConstructedWith($orderRepository, $itemRepository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Sylius\Bundle\SalesBundle\Builder\OrderBuilder');
    }

    function it_implements_Sylius_order_builder_interface()
    {
        $this->shouldImplement('Sylius\Bundle\SalesBundle\Builder\OrderBuilderInterface');
    }

    function it_throws_exception_if_trying_to_get_the_order_before_creating_it()
    {
        $this
            ->shouldThrow('LogicException')
            ->duringGetOrder()
        ;
    }

    /**
     * @param Sylius\Bundle\SalesBundle\Model\SellableInterface $sellable
     */
    function it_throws_exception_if_trying_to_add_an_item_before_creating_order($sellable)
    {
        $this
            ->shouldThrow('LogicException')
            ->duringAdd($sellable, 12.99)
        ;
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_creates_and_order_and_returns_itself($orderRepository, $order)
    {
        $orderRepository->createNew()->willReturn($order);

        $this->create()->shouldReturn($this);
        $this->getOrder()->shouldReturn($order);
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_returns_itself_when_starting_to_modify_order($order)
    {
        $this->modify($order)->shouldReturn($this);
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     */
    function it_returns_the_modified_order($order)
    {
        $this->modify($order);
        $this->getOrder()->shouldReturn($order);
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\SellableInterface $sellable
     * @param \Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item
     */
    function it_creates_an_order_item_for_given_sellable_unit_price_and_quantity($orderRepository, $itemRepository, $sellable, $order, $item)
    {
        $orderRepository->createNew()->willReturn($order);
        $itemRepository->createNew()->willReturn($item);

        $item->setSellable($sellable)->shouldBeCalled();
        $item->setUnitPrice(19.99)->shouldBeCalled();
        $item->setQuantity(6)->shouldBeCalled();

        $order->addItem($item)->shouldBeCalled();

        $this
            ->create()
            ->add($sellable, 19.99, 6)
        ;
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\SellableInterface $sellable
     * @param \Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item
     */
    function it_adds_sellable_with_quantity_equal_to_1_by_default($orderRepository, $itemRepository, $sellable, $order, $item)
    {
        $orderRepository->createNew()->willReturn($order);
        $itemRepository->createNew()->willReturn($item);

        $item->setSellable($sellable)->shouldBeCalled();
        $item->setUnitPrice(15)->shouldBeCalled();
        $item->setQuantity(1)->shouldBeCalled();

        $order->addItem($item)->shouldBeCalled();

        $this
            ->create()
            ->add($sellable, 15)
        ;
    }

    /**
     * @param \Sylius\Bundle\SalesBundle\Model\SellableInterface $sellable
     * @param \Sylius\Bundle\SalesBundle\Model\OrderInterface $order
     * @param \Sylius\Bundle\SalesBundle\Model\OrderItemInterface $item
     */
    function it_calculates_order_total_at_the_return($orderRepository, $itemRepository, $sellable, $order, $item)
    {
        $orderRepository->createNew()->willReturn($order);
        $itemRepository->createNew()->willReturn($item);

        $item->setSellable($sellable)->shouldBeCalled();
        $item->setUnitPrice(19.99)->shouldBeCalled();
        $item->setQuantity(6)->shouldBeCalled();

        $order->addItem($item)->shouldBeCalled();

        $this
            ->create()
            ->add($sellable, 19.99, 6)
        ;

        $order->calculateTotal()->shouldBeCalled();

        $this->getOrder()->shouldReturn($order);
    }
}
