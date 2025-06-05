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

namespace Tests\Sylius\Bundle\ApiBundle\Modifier;

use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Sylius\Abstraction\StateMachine\StateMachineInterface;
use Sylius\Bundle\ApiBundle\Mapper\AddressMapperInterface;
use Sylius\Bundle\ApiBundle\Modifier\OrderAddressModifier;
use Sylius\Component\Core\Model\AddressInterface;
use Sylius\Component\Core\Model\ChannelInterface;
use Sylius\Component\Core\Model\OrderInterface;
use Sylius\Component\Core\OrderCheckoutTransitions;

final class OrderAddressModifierTest extends TestCase
{
    private MockObject&StateMachineInterface $stateMachine;

    private AddressMapperInterface&MockObject $addressMapper;

    private OrderAddressModifier $orderAddressModifier;

    protected function setUp(): void
    {
        parent::setUp();
        $this->stateMachine = $this->createMock(StateMachineInterface::class);
        $this->addressMapper = $this->createMock(AddressMapperInterface::class);
        $this->orderAddressModifier = new OrderAddressModifier($this->stateMachine, $this->addressMapper);
    }

    public function testModifiesAddressesOfAnOrderWithoutProvidedShippingAddress(): void
    {
        $billingAddressMock = $this->createMock(AddressInterface::class);
        $orderMock = $this->createMock(OrderInterface::class);
        $channelMock = $this->createMock(ChannelInterface::class);

        $orderMock->expects(self::once())->method('getTokenValue')->willReturn('ORDERTOKEN');

        $orderMock->expects(self::once())->method('getShippingAddress')->willReturn(null);

        $orderMock->expects(self::once())->method('getBillingAddress')->willReturn(null);

        $orderMock->expects(self::once())->method('getChannel')->willReturn($channelMock);

        $channelMock->expects(self::once())->method('isShippingAddressInCheckoutRequired')->willReturn(false);

        $orderMock->expects(self::once())->method('setBillingAddress')->with($billingAddressMock);

        $orderMock->expects(self::once())->method('setShippingAddress')->with($this->isInstanceOf(AddressInterface::class));

        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($orderMock, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->willReturn(true);

        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with($orderMock, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS);

        $this->orderAddressModifier->modify($orderMock, $billingAddressMock, null);
    }

    public function testModifiesAddressesOfAnOrderWithoutProvidedBillingAddress(): void
    {
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        $orderMock = $this->createMock(OrderInterface::class);
        $channelMock = $this->createMock(ChannelInterface::class);

        $orderMock->expects(self::once())->method('getTokenValue')->willReturn('ORDERTOKEN');

        $orderMock->expects(self::once())->method('getShippingAddress')->willReturn(null);

        $orderMock->expects(self::once())->method('getBillingAddress')->willReturn(null);

        $orderMock->expects(self::once())->method('getChannel')->willReturn($channelMock);

        $channelMock->expects(self::once())->method('isShippingAddressInCheckoutRequired')->willReturn(true);

        $orderMock->expects(self::once())->method('setShippingAddress')->with($shippingAddressMock);

        $orderMock->expects(self::once())
            ->method('setBillingAddress')
            ->with($this->isInstanceOf(AddressInterface::class));

        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($orderMock, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->willReturn(true);

        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with($orderMock, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS);

        $this->orderAddressModifier->modify($orderMock, null, $shippingAddressMock);
    }

    public function testUpdatesOrderAddresses(): void
    {
        $newBillingAddressMock = $this->createMock(AddressInterface::class);
        $newShippingAddressMock = $this->createMock(AddressInterface::class);
        $oldBillingAddressMock = $this->createMock(AddressInterface::class);
        $oldShippingAddressMock = $this->createMock(AddressInterface::class);
        $orderMock = $this->createMock(OrderInterface::class);
        $channelMock = $this->createMock(ChannelInterface::class);

        $orderMock->expects(self::once())->method('getTokenValue')->willReturn('ORDERTOKEN');

        $orderMock->expects(self::once())->method('getBillingAddress')->willReturn($oldBillingAddressMock);

        $orderMock->expects(self::once())->method('getShippingAddress')->willReturn($oldShippingAddressMock);

        $orderMock->expects(self::once())->method('getChannel')->willReturn($channelMock);

        $channelMock->expects(self::once())->method('isShippingAddressInCheckoutRequired')->willReturn(false);

        $this->addressMapper->expects(self::exactly(2))
            ->method('mapExisting')
            ->willReturnCallback(function ($currentAddress, $targetAddress) use ($oldBillingAddressMock, $oldShippingAddressMock) {
                if ($currentAddress === $oldBillingAddressMock) {
                    return $oldBillingAddressMock;
                }
                if ($currentAddress === $oldShippingAddressMock) {
                    return $oldShippingAddressMock;
                }

                return null;
            });

        $orderMock->expects(self::once())->method('setBillingAddress')->with($oldBillingAddressMock);

        $orderMock->expects(self::once())->method('setShippingAddress')->with($oldShippingAddressMock);

        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($orderMock, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->willReturn(true);

        $this->stateMachine->expects(self::once())
            ->method('apply')
            ->with($orderMock, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS);

        $this->orderAddressModifier->modify(
            $orderMock,
            $newBillingAddressMock,
            $newShippingAddressMock,
        );
    }

    public function testThrowsAnExceptionIfOrderCannotBeAddressed(): void
    {
        $billingAddressMock = $this->createMock(AddressInterface::class);
        $shippingAddressMock = $this->createMock(AddressInterface::class);
        $orderMock = $this->createMock(OrderInterface::class);

        $this->stateMachine->expects(self::once())
            ->method('can')
            ->with($orderMock, OrderCheckoutTransitions::GRAPH, OrderCheckoutTransitions::TRANSITION_ADDRESS)
            ->willReturn(false);

        self::expectException(\LogicException::class);

        $this->orderAddressModifier->modify($orderMock, $billingAddressMock, $shippingAddressMock);
    }
}
