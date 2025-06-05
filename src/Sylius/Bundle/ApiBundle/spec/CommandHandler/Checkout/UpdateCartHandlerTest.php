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

use InvalidArgumentException;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use spec\Sylius\Bundle\ApiBundle\CommandHandler\MessageHandlerAttributeTrait;
use Sylius\Bundle\ApiBundle\Assigner\OrderPromotionCodeAssignerInterface;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\CommandHandler\Checkout\UpdateCartHandler;
use Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifierInterface;
use Sylius\Bundle\CoreBundle\Resolver\CustomerResolverInterface;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\CustomerInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;

final class UpdateCartHandlerTest extends TestCase
{
    /** @var OrderRepositoryInterface|MockObject */
    private MockObject $orderRepositoryMock;

    /** @var OrderAddressModifierInterface|MockObject */
    private MockObject $orderAddressModifierMock;

    /** @var OrderPromotionCodeAssignerInterface|MockObject */
    private MockObject $orderPromotionCodeAssignerMock;

    /** @var CustomerResolverInterface|MockObject */
    private MockObject $customerResolverMock;

    private UpdateCartHandler $updateCartHandler;

    use MessageHandlerAttributeTrait;

    protected function setUp(): void
    {
        $this->orderRepositoryMock = $this->createMock(OrderRepositoryInterface::class);
        $this->orderAddressModifierMock = $this->createMock(OrderAddressModifierInterface::class);
        $this->orderPromotionCodeAssignerMock = $this->createMock(OrderPromotionCodeAssignerInterface::class);
        $this->customerResolverMock = $this->createMock(CustomerResolverInterface::class);
        $this->updateCartHandler = new UpdateCartHandler($this->orderRepositoryMock, $this->orderAddressModifierMock, $this->orderPromotionCodeAssignerMock, $this->customerResolverMock);
    }

    public function testThrowsExceptionIfCartIsNotFound(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var AddressInterface|MockObject $shippingAddressMock */
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'cart'])->willReturn(null);
        $orderMock->expects(self::never())->method('setCustomer');
        $this->orderAddressModifierMock->expects(self::never())->method('modify')->with($orderMock, $billingAddressMock, $shippingAddressMock, 'john.doe@email.com');
        $this->orderPromotionCodeAssignerMock->expects(self::never())->method('assign')->with($orderMock, 'coupon');
        $updateCart = new UpdateCart(
            email: 'john.doe@email.com',
            billingAddress: $billingAddressMock,
            shippingAddress: $shippingAddressMock,
            couponCode: 'coupon',
            orderTokenValue: 'cart',
        );
        $this->expectException(InvalidArgumentException::class);
        $this->updateCartHandler->__invoke($updateCart);
    }

    public function testModifiesBillingAddress(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        $updateCart = new UpdateCart(
            billingAddress: $billingAddressMock,
            orderTokenValue: 'cart',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'cart'])->willReturn($orderMock);
        $orderMock->expects(self::never())->method('setCustomer');
        $this->orderAddressModifierMock->expects(self::once())->method('modify')->with($orderMock, $billingAddressMock, null)
            ->willReturn($orderMock)
        ;
        $this->orderPromotionCodeAssignerMock->expects(self::once())->method('assign')->with($orderMock, null)
            ->willReturn($orderMock)
        ;
        self::assertSame($orderMock, $this($updateCart));
    }

    public function testModifiesShippingAddress(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var AddressInterface|MockObject $shippingAddressMock */
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        $updateCart = new UpdateCart(
            shippingAddress: $shippingAddressMock,
            orderTokenValue: 'cart',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'cart'])->willReturn($orderMock);
        $orderMock->expects(self::never())->method('setCustomer');
        $this->orderAddressModifierMock->expects(self::once())->method('modify')->with($orderMock, null, $shippingAddressMock)
            ->willReturn($orderMock)
        ;
        $this->orderPromotionCodeAssignerMock->expects(self::once())->method('assign')->with($orderMock, null)
            ->willReturn($orderMock)
        ;
        self::assertSame($orderMock, $this($updateCart));
    }

    public function testAppliesCoupon(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        $updateCart = new UpdateCart(
            couponCode: 'couponCode',
            orderTokenValue: 'cart',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'cart'])->willReturn($orderMock);
        $orderMock->expects(self::never())->method('setCustomer');
        $this->orderAddressModifierMock->expects(self::never())->method('modify');
        $this->orderPromotionCodeAssignerMock->expects(self::once())->method('assign')->with($orderMock, 'couponCode')
            ->willReturn($orderMock)
        ;
        self::assertSame($orderMock, $this($updateCart));
    }

    public function testModifiesAddressAndEmailAndAppliesCoupon(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        /** @var AddressInterface|MockObject $shippingAddressMock */
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $updateCart = new UpdateCart(
            email: 'john.doe@email.com',
            billingAddress: $billingAddressMock,
            shippingAddress: $shippingAddressMock,
            couponCode: 'couponCode',
            orderTokenValue: 'cart',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'cart'])->willReturn($orderMock);
        $this->customerResolverMock->expects(self::once())->method('resolve')->with('john.doe@email.com')->willReturn($customerMock);
        $orderMock->expects(self::once())->method('setCustomer')->with($customerMock);
        $this->orderAddressModifierMock->expects(self::once())->method('modify')->with($orderMock, $billingAddressMock, $shippingAddressMock)
            ->willReturn($orderMock)
        ;
        $this->orderPromotionCodeAssignerMock->expects(self::once())->method('assign')->with($orderMock, 'couponCode')
            ->willReturn($orderMock)
        ;
        self::assertSame($orderMock, $this($updateCart));
    }

    public function testSetsTheCustomerByEmail(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        /** @var AddressInterface|MockObject $shippingAddressMock */
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        /** @var CustomerInterface|MockObject $customerMock */
        $customerMock = $this->createMock(CustomerInterface::class);
        $updateCart = new UpdateCart(
            email: 'john.doe@email.com',
            billingAddress: $billingAddressMock,
            shippingAddress: $shippingAddressMock,
            couponCode: 'couponCode',
            orderTokenValue: 'cart',
        );
        $this->orderRepositoryMock->expects(self::once())->method('findOneBy')->with(['tokenValue' => 'cart'])->willReturn($orderMock);
        $this->customerResolverMock->expects(self::once())->method('resolve')->with('john.doe@email.com')->willReturn($customerMock);
        $orderMock->expects(self::once())->method('setCustomer')->with($customerMock);
        $this->orderAddressModifierMock->expects(self::once())->method('modify')->with($orderMock, $billingAddressMock, $shippingAddressMock)
            ->willReturn($orderMock)
        ;
        $this->orderPromotionCodeAssignerMock->expects(self::once())->method('assign')->with($orderMock, 'couponCode')
            ->willReturn($orderMock)
        ;
        self::assertSame($orderMock, $this($updateCart));
    }
}
