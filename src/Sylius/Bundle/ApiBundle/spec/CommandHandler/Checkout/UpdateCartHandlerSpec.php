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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssignerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class UpdateCartHandlerSpec extends ObjectBehavior
{
    function let(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromotionCodeAssignerInterface $orderPromotionCodeAssigner,
        CustomerResolverInterface $customerResolver,
    ) {
        $this->beConstructedWith(
            $orderRepository,
            $orderAddressModifier,
            $orderPromotionCodeAssigner,
            $customerResolver,
        );
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_throws_exception_if_cart_is_not_found(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromotionCodeAssignerInterface $orderPromotionCodeAssigner,
        OrderInterface $order,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'cart'])->willReturn(null);

        $order->setCustomer(Argument::any())->shouldNotBeCalled();

        $orderAddressModifier->modify($order, $billingAddress, $shippingAddress, 'john.doe@email.com')->shouldNotBeCalled();

        $orderPromotionCodeAssigner->assign($order, 'coupon')->shouldNotBeCalled();

        $updateCart = new UpdateCart(
            email: 'john.doe@email.com',
            billingAddress: $billingAddress->getWrappedObject(),
            shippingAddress: $shippingAddress->getWrappedObject(),
            couponCode: 'coupon',
            orderTokenValue: 'cart',
        );

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$updateCart])
        ;
    }

    function it_modifies_billing_address(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromotionCodeAssignerInterface $orderPromotionCodeAssigner,
        OrderInterface $order,
        AddressInterface $billingAddress,
    ): void {
        $updateCart = new UpdateCart(
            billingAddress: $billingAddress->getWrappedObject(),
            orderTokenValue: 'cart',
        );

        $orderRepository->findOneBy(['tokenValue' => 'cart'])->willReturn($order);

        $order->setCustomer(Argument::any())->shouldNotBeCalled();

        $orderAddressModifier->modify($order, $billingAddress->getWrappedObject(), null)
            ->shouldBeCalled()
            ->willReturn($order)
        ;

        $orderPromotionCodeAssigner
            ->assign($order->getWrappedObject(), null)
            ->shouldBeCalled()
            ->willReturn($order->getWrappedObject())
        ;

        $this($updateCart)->shouldReturn($order);
    }

    function it_modifies_shipping_address(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromotionCodeAssignerInterface $orderPromotionCodeAssigner,
        OrderInterface $order,
        AddressInterface $shippingAddress,
    ): void {
        $updateCart = new UpdateCart(
            shippingAddress: $shippingAddress->getWrappedObject(),
            orderTokenValue: 'cart',
        );

        $orderRepository->findOneBy(['tokenValue' => 'cart'])->willReturn($order->getWrappedObject());

        $order->setCustomer(Argument::any())->shouldNotBeCalled();

        $orderAddressModifier->modify(
            $order->getWrappedObject(),
            null,
            $shippingAddress->getWrappedObject(),
        )
            ->shouldBeCalled()
            ->willReturn($order)
        ;

        $orderPromotionCodeAssigner
            ->assign($order, null)
            ->shouldBeCalled()
            ->willReturn($order)
        ;

        $this($updateCart)->shouldReturn($order);
    }

    function it_applies_coupon(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromotionCodeAssignerInterface $orderPromotionCodeAssigner,
        OrderInterface $order,
    ): void {
        $updateCart = new UpdateCart(
            couponCode: 'couponCode',
            orderTokenValue: 'cart',
        );

        $orderRepository->findOneBy(['tokenValue' => 'cart'])->willReturn($order->getWrappedObject());

        $order->setCustomer(Argument::any())->shouldNotBeCalled();

        $orderAddressModifier->modify($order->getWrappedObject(), Argument::any())->shouldNotBeCalled();

        $orderPromotionCodeAssigner
            ->assign($order->getWrappedObject(), 'couponCode')
            ->shouldBeCalled()
            ->willReturn($order->getWrappedObject())
        ;

        $this($updateCart)->shouldReturn($order);
    }

    function it_modifies_address_and_email_and_applies_coupon(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromotionCodeAssignerInterface $orderPromotionCodeAssigner,
        OrderInterface $order,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        CustomerInterface $customer,
        CustomerResolverInterface $customerResolver,
    ): void {
        $updateCart = new UpdateCart(
            email: 'john.doe@email.com',
            billingAddress: $billingAddress->getWrappedObject(),
            shippingAddress: $shippingAddress->getWrappedObject(),
            couponCode: 'couponCode',
            orderTokenValue: 'cart',
        );

        $orderRepository->findOneBy(['tokenValue' => 'cart'])->willReturn($order->getWrappedObject());

        $customerResolver->resolve('john.doe@email.com')->shouldBeCalled()->willReturn($customer);
        $order->setCustomer($customer)->shouldBeCalled();

        $orderAddressModifier->modify(
            $order->getWrappedObject(),
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject(),
        )
            ->shouldBeCalled()
            ->willReturn($order)
        ;

        $orderPromotionCodeAssigner
            ->assign($order->getWrappedObject(), 'couponCode')
            ->shouldBeCalled()
            ->willReturn($order->getWrappedObject())
        ;

        $this($updateCart)->shouldReturn($order);
    }

    function it_sets_the_customer_by_email(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromotionCodeAssignerInterface $orderPromotionCodeAssigner,
        OrderInterface $order,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        CustomerResolverInterface $customerResolver,
        CustomerInterface $customer,
    ): void {
        $updateCart = new UpdateCart(
            email: 'john.doe@email.com',
            billingAddress: $billingAddress->getWrappedObject(),
            shippingAddress: $shippingAddress->getWrappedObject(),
            couponCode: 'couponCode',
            orderTokenValue: 'cart',
        );

        $orderRepository->findOneBy(['tokenValue' => 'cart'])->willReturn($order->getWrappedObject());

        $customerResolver->resolve('john.doe@email.com')->shouldBeCalled()->willReturn($customer);
        $order->setCustomer($customer)->shouldBeCalled();

        $orderAddressModifier->modify(
            $order->getWrappedObject(),
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject(),
        )
            ->shouldBeCalled()
            ->willReturn($order->getWrappedObject())
        ;

        $orderPromotionCodeAssigner
            ->assign($order->getWrappedObject(), 'couponCode')
            ->shouldBeCalled()
            ->willReturn($order->getWrappedObject())
        ;

        $this($updateCart)->shouldReturn($order);
    }
}
