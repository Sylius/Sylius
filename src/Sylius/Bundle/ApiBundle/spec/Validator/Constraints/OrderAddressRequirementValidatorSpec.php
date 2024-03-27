<?php

/*
 * This file is part of the Sylius package.
 *
 * (c) Sylius Sp. z o.o.
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

namespace spec\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PhpSpec\ObjectBehavior;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderAddressRequirement;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class OrderAddressRequirementValidatorSpec extends ObjectBehavior
{
    const MESSAGE = 'sylius.order.address_requirement';

    function let(OrderRepositoryInterface $orderRepository, ExecutionContextInterface $context): void
    {
        $this->beConstructedWith($orderRepository);

        $this->initialize($context);
    }

    function it_throws_exception_if_constraint_is_not_an_instance_of_order_address_requirement(
        Constraint $constraint,
    ): void {
        $this
            ->shouldThrow(UnexpectedTypeException::class)
            ->during('validate', ['product_code', $constraint])
        ;
    }

    function it_throws_exception_if_value_is_not_an_instance_of_update_cart(
        OrderInterface $order,
    ): void {
        $this
            ->shouldThrow(UnexpectedValueException::class)
            ->during('validate', [$order, new OrderAddressRequirement()])
        ;
    }

    function it_does_nothing_if_billing_and_shipping_addresses_are_not_provided(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ChannelInterface $channel,
        ExecutionContextInterface $context,
    ): void {
        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $channel->isShippingAddressInCheckoutRequired()->willReturn(false);

        $updateCart = new UpdateCart();
        $updateCart->setOrderTokenValue('TOKEN');

        $this->validate($updateCart, new OrderAddressRequirement());

        $context->addViolation(self::MESSAGE, ['%addressName%' => 'billingAddress'])->shouldNotHaveBeenCalled();
        $context->addViolation(self::MESSAGE, ['%addressName%' => 'shippingAddress'])->shouldNotHaveBeenCalled();
    }

    function it_throw_exception_if_order_not_found(
        OrderRepositoryInterface $orderRepository,
        AddressInterface $billingAddress,
    ): void {
        $orderRepository->findCartByTokenValue('TOKEN')->willReturn(null);

        $updateCart = new UpdateCart(billingAddress: $billingAddress->getWrappedObject());
        $updateCart->setOrderTokenValue('TOKEN');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$updateCart, new OrderAddressRequirement()])
        ;
    }

    function it_throw_exception_if_order_does_not_have_channel(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        AddressInterface $billingAddress,
    ): void {
        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($order);
        $order->getChannel()->willReturn(null);

        $updateCart = new UpdateCart(billingAddress: $billingAddress->getWrappedObject());
        $updateCart->setOrderTokenValue('TOKEN');

        $this
            ->shouldThrow(\InvalidArgumentException::class)
            ->during('validate', [$updateCart, new OrderAddressRequirement()])
        ;
    }

    function it_does_nothing_if_shipping_address_is_required_and_provided(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ChannelInterface $channel,
        AddressInterface $shippingAddress,
        ExecutionContextInterface $context,
    ): void {
        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $channel->isShippingAddressInCheckoutRequired()->willReturn(true);

        $updateCart = new UpdateCart(shippingAddress: $shippingAddress->getWrappedObject());
        $updateCart->setOrderTokenValue('TOKEN');

        $this->validate($updateCart, new OrderAddressRequirement());

        $context->addViolation(self::MESSAGE, ['%addressName%' => 'shippingAddress'])->shouldNotHaveBeenCalled();
    }

    function it_does_nothing_if_billing_address_is_required_and_provided(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        ChannelInterface $channel,
        AddressInterface $billingAddress,
        ExecutionContextInterface $context,
    ): void {
        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $channel->isShippingAddressInCheckoutRequired()->willReturn(false);

        $updateCart = new UpdateCart(billingAddress: $billingAddress->getWrappedObject());
        $updateCart->setOrderTokenValue('TOKEN');

        $this->validate($updateCart, new OrderAddressRequirement());

        $context->addViolation(self::MESSAGE, ['%addressName%' => 'billingAddress'])->shouldNotHaveBeenCalled();
    }

    function it_adds_violation_if_shipping_address_is_required_but_not_provided(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        AddressInterface $billingAddress,
        ChannelInterface $channel,
        ExecutionContextInterface $context,
    ): void {
        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $channel->isShippingAddressInCheckoutRequired()->willReturn(true);

        $updateCart = new UpdateCart(billingAddress: $billingAddress->getWrappedObject());
        $updateCart->setOrderTokenValue('TOKEN');

        $this->validate($updateCart, new OrderAddressRequirement());

        $context->addViolation(self::MESSAGE, ['%addressName%' => 'shippingAddress'])->shouldHaveBeenCalled();
    }

    function it_adds_violation_if_billing_address_is_required_but_not_provided(
        OrderRepositoryInterface $orderRepository,
        OrderInterface $order,
        AddressInterface $shippingAddress,
        ChannelInterface $channel,
        ExecutionContextInterface $context,
    ): void {
        $orderRepository->findCartByTokenValue('TOKEN')->willReturn($order);
        $order->getChannel()->willReturn($channel);
        $channel->isShippingAddressInCheckoutRequired()->willReturn(false);

        $updateCart = new UpdateCart(shippingAddress: $shippingAddress->getWrappedObject());
        $updateCart->setOrderTokenValue('TOKEN');

        $this->validate($updateCart, new OrderAddressRequirement());

        $context->addViolation(self::MESSAGE, ['%addressName%' => 'billingAddress'])->shouldHaveBeenCalled();
    }
}
