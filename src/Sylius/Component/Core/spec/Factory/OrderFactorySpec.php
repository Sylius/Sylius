<?php

namespace spec\Sylius\Component\Core\Factory;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\TokenAssigner\OrderTokenAssignerInterface;
use Sylius\Component\Resource\Factory\FactoryInterface;

class OrderFactorySpec extends ObjectBehavior
{
    function let(FactoryInterface $decoratedFactory, OrderTokenAssignerInterface $tokenAssigner): void
    {
        $this->beConstructedWith($decoratedFactory, $tokenAssigner);
    }

    function it_is_a_resource_factory(): void
    {
        $this->shouldImplement(FactoryInterface::class);
    }

    function it_creates_a_new_cart(OrderTokenAssignerInterface $tokenAssigner, FactoryInterface $decoratedFactory, OrderInterface $order): void
    {
        $decoratedFactory->createNew()->willReturn($order);
        $tokenAssigner->assignTokenValue($order)->shouldBeCalled();

        $this->createNew()->shouldReturn($order);
    }
}
