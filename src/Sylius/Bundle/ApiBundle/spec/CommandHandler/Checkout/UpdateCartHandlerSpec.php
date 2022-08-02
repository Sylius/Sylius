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

namespace spec\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use PhpSpec\ObjectBehavior;
use Prophecy\Argument;
use Sylius\Bundle\ApiBundle\Assigner\OrderPromoCodeAssignerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface;
use Sylius\Bundle\ApiBundle\Provider\CustomerProviderInterface;
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
        OrderPromoCodeAssignerInterface $orderPromoCodeAssigner,
        CustomerProviderInterface $customerProvider,
    ) {
        $this->beConstructedWith($orderRepository, $orderAddressModifier, $orderPromoCodeAssigner, $customerProvider);
    }

    function it_is_a_message_handler(): void
    {
        $this->shouldImplement(MessageHandlerInterface::class);
    }

    function it_throws_exception_if_cart_is_not_found(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromoCodeAssignerInterface $orderPromoCodeAssigner,
        OrderInterface $order,
        AddressInterface $shippingAddress,
        AddressInterface $billingAddress,
    ): void {
        $orderRepository->findOneBy(['tokenValue' => 'cart'])->willReturn(null);

        $order->setCustomer(Argument::any())->shouldNotBeCalled();

        $orderAddressModifier->modify($order, $billingAddress, $shippingAddress, 'john.doe@email.com')->shouldNotBeCalled();

        $orderPromoCodeAssigner->assign($order, 'coupon')->shouldNotBeCalled();

        $updateCart = new UpdateCart('john.doe@email.com', $billingAddress->getWrappedObject(), $shippingAddress->getWrappedObject(), 'coupon');
        $updateCart->setOrderTokenValue('cart');

        $this->shouldThrow(\InvalidArgumentException::class)
            ->during('__invoke', [$updateCart])
        ;
    }

    function it_modifies_billing_address(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromoCodeAssignerInterface $orderPromoCodeAssigner,
        OrderInterface $order,
        AddressInterface $billingAddress,
    ): void {
        $updateCart = new UpdateCart(null, $billingAddress->getWrappedObject());
        $updateCart->setOrderTokenValue('cart');

        $orderRepository->findOneBy(['tokenValue' => 'cart'])->willReturn($order);

        $order->setCustomer(Argument::any())->shouldNotBeCalled();

        $orderAddressModifier->modify($order, $billingAddress->getWrappedObject(), null)
            ->shouldBeCalled()
            ->willReturn($order)
        ;

        $orderPromoCodeAssigner
            ->assign($order->getWrappedObject(), null)
            ->shouldBeCalled()
            ->willReturn($order->getWrappedObject())
        ;

        $this($updateCart)->shouldReturn($order);
    }

    function it_modifies_shipping_address(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromoCodeAssignerInterface $orderPromoCodeAssigner,
        OrderInterface $order,
        AddressInterface $shippingAddress,
    ): void {
        $updateCart = new UpdateCart(null, null, $shippingAddress->getWrappedObject(), null);

        $updateCart->setOrderTokenValue('cart');

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

        $orderPromoCodeAssigner
            ->assign($order, null)
            ->shouldBeCalled()
            ->willReturn($order)
        ;

        $this($updateCart)->shouldReturn($order);
    }

    function it_applies_coupon(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromoCodeAssignerInterface $orderPromoCodeAssigner,
        OrderInterface $order,
    ): void {
        $updateCart = new UpdateCart(null, null, null, 'couponCode');
        $updateCart->setOrderTokenValue('cart');

        $orderRepository->findOneBy(['tokenValue' => 'cart'])->willReturn($order->getWrappedObject());

        $order->setCustomer(Argument::any())->shouldNotBeCalled();

        $orderAddressModifier->modify($order->getWrappedObject(), Argument::any())->shouldNotBeCalled();

        $orderPromoCodeAssigner
            ->assign($order->getWrappedObject(), 'couponCode')
            ->shouldBeCalled()
            ->willReturn($order->getWrappedObject())
        ;

        $this($updateCart)->shouldReturn($order);
    }

    function it_modifies_address_and_email_and_applies_coupon(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromoCodeAssignerInterface $orderPromoCodeAssigner,
        OrderInterface $order,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        CustomerInterface $customer,
        CustomerProviderInterface $customerProvider,
    ): void {
        $updateCart = new UpdateCart(
            'john.doe@email.com',
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject(),
            'couponCode',
        );

        $updateCart->setOrderTokenValue('cart');

        $orderRepository->findOneBy(['tokenValue' => 'cart'])->willReturn($order->getWrappedObject());

        $customerProvider->provide('john.doe@email.com')->shouldBeCalled()->willReturn($customer);
        $order->setCustomer($customer)->shouldBeCalled();

        $orderAddressModifier->modify(
            $order->getWrappedObject(),
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject(),
        )
            ->shouldBeCalled()
            ->willReturn($order)
        ;

        $orderPromoCodeAssigner
            ->assign($order->getWrappedObject(), 'couponCode')
            ->shouldBeCalled()
            ->willReturn($order->getWrappedObject())
        ;

        $this($updateCart)->shouldReturn($order);
    }

    function it_sets_the_customer_by_email(
        OrderRepositoryInterface $orderRepository,
        OrderAddressModifierInterface $orderAddressModifier,
        OrderPromoCodeAssignerInterface $orderPromoCodeAssigner,
        OrderInterface $order,
        AddressInterface $billingAddress,
        AddressInterface $shippingAddress,
        CustomerProviderInterface $customerProvider,
        CustomerInterface $customer,
    ): void {
        $updateCart = new UpdateCart(
            'john.doe@email.com',
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject(),
            'couponCode',
        );

        $updateCart->setOrderTokenValue('cart');

        $orderRepository->findOneBy(['tokenValue' => 'cart'])->willReturn($order->getWrappedObject());

        $customerProvider->provide('john.doe@email.com')->shouldBeCalled()->willReturn($customer);
        $order->setCustomer($customer)->shouldBeCalled();

        $orderAddressModifier->modify(
            $order->getWrappedObject(),
            $billingAddress->getWrappedObject(),
            $shippingAddress->getWrappedObject(),
        )
            ->shouldBeCalled()
            ->willReturn($order->getWrappedObject())
        ;

        $orderPromoCodeAssigner
            ->assign($order->getWrappedObject(), 'couponCode')
            ->shouldBeCalled()
            ->willReturn($order->getWrappedObject())
        ;

        $this($updateCart)->shouldReturn($order);
    }
}
