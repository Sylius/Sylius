<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Paweł Jędrzejewski
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Component\Core\Order\Requirements;

use PhpSpec\ObjectBehavior;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\OrderInterface;

class RequiredShippingSpecificationSpec extends ObjectBehavior
{
    function it_is_not_satisfied_by_anonymous_object(): void
    {
        $this->isSatisfiedBy(new class(){})->shouldBe(false);
    }

    function it_is_satisfied_by_order_with_required_shipping_and_address(OrderInterface $order, AddressInterface $address): void
    {
        $order->isShippingRequired()->willReturn(true);
        $order->getShippingAddress()->willReturn($address);

        $this->isSatisfiedBy($order)->shouldBe(true);
    }

    function it_is_satisfied_by_order_with_not_required_shipping(OrderInterface $order, AddressInterface $address): void
    {
        $order->isShippingRequired()->willReturn(false);
        $order->getShippingAddress()->willReturn($address);

        $this->isSatisfiedBy($order)->shouldBe(true);
    }

    function it_is_satisfied_by_order_with_not_required_shipping_and_empty_address(OrderInterface $order): void
    {
        $order->isShippingRequired()->willReturn(false);
        $order->getShippingAddress()->willReturn(null);

        $this->isSatisfiedBy($order)->shouldBe(true);
    }

    function it_is_not_satisfied_by_order_with_required_shipping_and_empty_address(OrderInterface $order): void
    {
        $order->isShippingRequired()->willReturn(true);
        $order->getShippingAddress()->willReturn(null);

        $this->isSatisfiedBy($order)->shouldBe(false);
    }
}
