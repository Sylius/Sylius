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

namespace Tests\Sylius\Bundle\ApiBundle\CommandHandler\Checkout;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssignerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\UpdateCartHandler;
use Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Tests\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;

final class UpdateCartHandlerTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private MockObject&OrderAddressModifierInterface $orderAddressModifier;

    private MockObject&OrderPromotionCodeAssignerInterface $orderPromotionCodeAssigner;

    private CustomerResolverInterface&MockObject $customerResolver;

    private UpdateCartHandler $handler;

    private MockObject&OrderInterface $order;

    private AddressInterface&MockObject $billingAddress;

    private AddressInterface&MockObject $shippingAddress;

    private CustomerInterface&MockObject $customer;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->orderAddressModifier = $this->createMock(OrderAddressModifierInterface::class);
        $this->orderPromotionCodeAssigner = $this->createMock(OrderPromotionCodeAssignerInterface::class);
        $this->customerResolver = $this->createMock(CustomerResolverInterface::class);
        $this->handler = new UpdateCartHandler(
            $this->orderRepository,
            $this->orderAddressModifier,
            $this->orderPromotionCodeAssigner,
            $this->customerResolver,
        );
        $this->order = $this->createMock(OrderInterface::class);
        $this->billingAddress = $this->createMock(AddressInterface::class);
        $this->shippingAddress = $this->createMock(AddressInterface::class);
        $this->customer = $this->createMock(CustomerInterface::class);
    }

    public function testThrowsExceptionIfCartIsNotFound(): void
    {
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'cart'])
            ->willReturn(null);
        $this->order->expects(self::never())->method('setCustomer');
        $this->orderAddressModifier->expects(self::never())
            ->method('modify')
            ->with($this->order, $this->billingAddress, $this->shippingAddress, 'john.doe@email.com');
        $this->orderPromotionCodeAssigner->expects(self::never())
            ->method('assign')
            ->with($this->order, 'coupon');
        $updateCart = new UpdateCart(
            email: 'john.doe@email.com',
            billingAddress: $this->billingAddress,
            shippingAddress: $this->shippingAddress,
            couponCode: 'coupon',
            orderTokenValue: 'cart',
        );
        self::expectException(\InvalidArgumentException::class);
        $this->handler->__invoke($updateCart);
    }

    public function testModifiesBillingAddress(): void
    {
        $updateCart = new UpdateCart(
            billingAddress: $this->billingAddress,
            orderTokenValue: 'cart',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'cart'])
            ->willReturn($this->order);
        $this->order->expects(self::never())
            ->method('setCustomer');
        $this->orderAddressModifier
            ->expects(self::once())
            ->method('modify')
            ->with($this->order, $this->billingAddress, null)
            ->willReturn($this->order);
        $this->orderPromotionCodeAssigner->expects(self::once())
            ->method('assign')
            ->with($this->order, null)
            ->willReturn($this->order);
        self::assertSame($this->order, $this->handler->__invoke($updateCart));
    }

    public function testModifiesShippingAddress(): void
    {
        $updateCart = new UpdateCart(
            shippingAddress: $this->shippingAddress,
            orderTokenValue: 'cart',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'cart'])
            ->willReturn($this->order);
        $this->order->expects(self::never())->method('setCustomer');
        $this->orderAddressModifier->expects(self::once())
            ->method('modify')
            ->with($this->order, null, $this->shippingAddress)
            ->willReturn($this->order);
        $this->orderPromotionCodeAssigner->expects(self::once())
            ->method('assign')
            ->with($this->order, null)
            ->willReturn($this->order);
        self::assertSame($this->order, $this->handler->__invoke($updateCart));
    }

    public function testAppliesCoupon(): void
    {
        $updateCart = new UpdateCart(
            couponCode: 'couponCode',
            orderTokenValue: 'cart',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'cart'])
            ->willReturn($this->order);
        $this->order->expects(self::never())->method('setCustomer');
        $this->orderAddressModifier->expects(self::never())->method('modify');
        $this->orderPromotionCodeAssigner
            ->expects(self::once())
            ->method('assign')
            ->with($this->order, 'couponCode')
            ->willReturn($this->order);
        self::assertSame($this->order, $this->handler->__invoke($updateCart));
    }

    public function testModifiesAddressAndEmailAndAppliesCoupon(): void
    {
        $updateCart = new UpdateCart(
            email: 'john.doe@email.com',
            billingAddress: $this->billingAddress,
            shippingAddress: $this->shippingAddress,
            couponCode: 'couponCode',
            orderTokenValue: 'cart',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'cart'])
            ->willReturn($this->order);
        $this->customerResolver->expects(self::once())
            ->method('resolve')
            ->with('john.doe@email.com')
            ->willReturn($this->customer);
        $this->order->expects(self::once())->method('setCustomer')->with($this->customer);
        $this->orderAddressModifier->expects(self::once())
            ->method('modify')
            ->with($this->order, $this->billingAddress, $this->shippingAddress)
            ->willReturn($this->order);
        $this->orderPromotionCodeAssigner->expects(self::once())
            ->method('assign')
            ->with($this->order, 'couponCode')
            ->willReturn($this->order);
        self::assertSame($this->order, $this->handler->__invoke($updateCart));
    }

    public function testSetsTheCustomerByEmail(): void
    {
        $updateCart = new UpdateCart(
            email: 'john.doe@email.com',
            billingAddress: $this->billingAddress,
            shippingAddress: $this->shippingAddress,
            couponCode: 'couponCode',
            orderTokenValue: 'cart',
        );
        $this->orderRepository->expects(self::once())
            ->method('findOneBy')
            ->with(['tokenValue' => 'cart'])
            ->willReturn($this->order);
        $this->customerResolver->expects(self::once())
            ->method('resolve')
            ->with('john.doe@email.com')
            ->willReturn($this->customer);
        $this->order->expects(self::once())->method('setCustomer')->with($this->customer);
        $this->orderAddressModifier->expects(self::once())
            ->method('modify')
            ->with($this->order, $this->billingAddress, $this->shippingAddress)
            ->willReturn($this->order);
        $this->orderPromotionCodeAssigner->expects(self::once())
            ->method('assign')
            ->with($this->order, 'couponCode')
            ->willReturn($this->order);
        self::assertSame($this->order, $this->handler->__invoke($updateCart));
    }
}
