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

namespace Tests\Sylius\Bundle\ApiBundle\Validator\Constraints;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Bundle\ApiBundle\Command\Checkout\UpdateCart;
use Sylius\Bundle\ApiBundle\Exception\ChannelNotFoundException;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderAddressRequirement;
use Sylius\Bundle\ApiBundle\Validator\Constraints\OrderAddressRequirementValidator;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\Repository\OrderRepositoryInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Context\ExecutionContextInterface;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

final class OrderAddressRequirementValidatorTest extends TestCase
{
    private MockObject&OrderRepositoryInterface $orderRepository;

    private ExecutionContextInterface&MockObject $context;

    private OrderAddressRequirementValidator $orderAddressRequirementValidator;

    public const MESSAGE = 'sylius.order.address_requirement';

    protected function setUp(): void
    {
        parent::setUp();
        $this->orderRepository = $this->createMock(OrderRepositoryInterface::class);
        $this->context = $this->createMock(ExecutionContextInterface::class);
        $this->orderAddressRequirementValidator = new OrderAddressRequirementValidator($this->orderRepository);
        $this->orderAddressRequirementValidator->initialize($this->context);
    }

    public function testThrowsAnExceptionIfConstraintIsNotAnInstanceOfOrderAddressRequirement(): void
    {
        /** @var Constraint|MockObject $constraintMock */
        $constraintMock = $this->createMock(Constraint::class);
        self::expectException(UnexpectedTypeException::class);
        $this->orderAddressRequirementValidator->validate('product_code', $constraintMock);
    }

    public function testThrowsAnExceptionIfValueIsNotAnInstanceOfUpdateCart(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        self::expectException(UnexpectedValueException::class);
        $this->orderAddressRequirementValidator->validate($orderMock, new OrderAddressRequirement());
    }

    public function testDoesNothingIfBillingAndShippingAddressesAreNotProvided(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $updateCart = new UpdateCart('token');
        $this->orderAddressRequirementValidator->validate($updateCart, new OrderAddressRequirement());
        $this->context->expects(self::never())->method('addViolation')->with(self::MESSAGE, ['%addressName%' => 'billingAddress']);
        $this->context->expects(self::never())->method('addViolation')->with(self::MESSAGE, ['%addressName%' => 'shippingAddress']);
    }

    public function testThrowsAnExceptionIfOrderIsNotFound(): void
    {
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn(null);
        $updateCart = new UpdateCart(billingAddress: $billingAddressMock, orderTokenValue: 'TOKEN');
        self::expectException(ChannelNotFoundException::class);
        $this->orderAddressRequirementValidator->validate($updateCart, new OrderAddressRequirement());
    }

    public function testThrowsAnExceptionIfOrderDoesNotHaveChannel(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getChannel')->willReturn(null);
        $updateCart = new UpdateCart(billingAddress: $billingAddressMock, orderTokenValue: 'TOKEN');
        self::expectException(ChannelNotFoundException::class);
        $this->orderAddressRequirementValidator->validate($updateCart, new OrderAddressRequirement());
    }

    public function testDoesNothingIfShippingAddressIsRequiredAndProvided(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var AddressInterface|MockObject $shippingAddressMock */
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getChannel')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('isShippingAddressInCheckoutRequired')->willReturn(true);
        $updateCart = new UpdateCart(shippingAddress: $shippingAddressMock, orderTokenValue: 'TOKEN');
        $this->orderAddressRequirementValidator->validate($updateCart, new OrderAddressRequirement());
        $this->context->expects(self::never())->method('addViolation')->with(self::MESSAGE, ['%addressName%' => 'shippingAddress']);
    }

    public function testDoesNothingIfBillingAddressIsRequiredAndProvided(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getChannel')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('isShippingAddressInCheckoutRequired')->willReturn(false);
        $updateCart = new UpdateCart(billingAddress: $billingAddressMock, orderTokenValue: 'TOKEN');
        $this->orderAddressRequirementValidator->validate($updateCart, new OrderAddressRequirement());
        $this->context->expects(self::never())->method('addViolation')->with(self::MESSAGE, ['%addressName%' => 'billingAddress']);
    }

    public function testAddsViolationIfShippingAddressIsRequiredButNotProvided(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var AddressInterface|MockObject $billingAddressMock */
        $billingAddressMock = $this->createMock(AddressInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getChannel')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('isShippingAddressInCheckoutRequired')->willReturn(true);
        $updateCart = new UpdateCart(billingAddress: $billingAddressMock, orderTokenValue: 'TOKEN');
        $this->orderAddressRequirementValidator->validate($updateCart, new OrderAddressRequirement());
        $this->context->expects(self::never())->method('addViolation')->with(self::MESSAGE, ['%addressName%' => 'shipping address']);
    }

    public function testAddsViolationIfBillingAddressIsRequiredButNotProvided(): void
    {
        /** @var OrderInterface|MockObject $orderMock */
        $orderMock = $this->createMock(OrderInterface::class);
        /** @var AddressInterface|MockObject $shippingAddressMock */
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        /** @var ChannelInterface|MockObject $channelMock */
        $channelMock = $this->createMock(ChannelInterface::class);
        $this->orderRepository->expects(self::once())->method('findCartByTokenValue')->with('TOKEN')->willReturn($orderMock);
        $orderMock->expects(self::once())->method('getChannel')->willReturn($channelMock);
        $channelMock->expects(self::once())->method('isShippingAddressInCheckoutRequired')->willReturn(false);
        $updateCart = new UpdateCart(shippingAddress: $shippingAddressMock, orderTokenValue: 'TOKEN');
        $this->orderAddressRequirementValidator->validate($updateCart, new OrderAddressRequirement());
        $this->context->expects(self::never())->method('addViolation')->with(self::MESSAGE, ['%addressName%' => 'billing address']);
    }
}
